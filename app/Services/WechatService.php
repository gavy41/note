<?php
// app/Services/WechatService.php
// 封装微信服务端 API：access_token 自动刷新 + OCR 识别

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WechatService
{
    private string $appId;
    private string $appSecret;

    public function __construct()
    {
        $this->appId     = config('wechat.app_id');
        $this->appSecret = config('wechat.app_secret');
    }

    /**
     * 获取 access_token（缓存 7000 秒，过期前自动刷新）
     */
    public function getAccessToken(): string
    {
        return Cache::remember('wechat_access_token', 7000, function () {
            $res = Http::get('https://api.weixin.qq.com/cgi-bin/token', [
                'grant_type' => 'client_credential',
                'appid'      => $this->appId,
                'secret'     => $this->appSecret,
            ]);

            $data = $res->json();

            if (!isset($data['access_token'])) {
                Log::error('获取微信 access_token 失败', $data);
                throw new \RuntimeException('获取微信 access_token 失败');
            }

            return $data['access_token'];
        });
    }

    /**
     * 调用微信 OCR 通用文字识别接口
     *
     * 微信限制：图片 ≤ 4MB，最长边 ≤ 4096px
     * 本方法会在发送前自动压缩图片到合规尺寸。
     *
     * @param  string $imagePath  本地图片绝对路径
     * @return string             识别出的文字（多行用 \n 分隔）
     */
    public function ocrImage(string $imagePath): string
    {
        $token = $this->getAccessToken();

        // 压缩图片，确保符合微信限制（≤4MB，最长边≤4096px）
        $compressedPath = $this->compressImage($imagePath);

        try {
            $res = Http::attach('img', file_get_contents($compressedPath), 'image.jpg')
                ->post("https://api.weixin.qq.com/cv/ocr/comm?access_token={$token}");
        } finally {
            // 清理压缩后的临时文件（如果与原文件不同）
            if ($compressedPath !== $imagePath && file_exists($compressedPath)) {
                @unlink($compressedPath);
            }
        }

        $data = $res->json();

        if (!isset($data['items'])) {
            // errcode 80001 = 图片无文字，不算错误
            if (($data['errcode'] ?? 0) === 80001) {
                return '';
            }
            Log::error('微信 OCR 失败', $data);
            throw new \RuntimeException('OCR 识别失败：' . ($data['errmsg'] ?? 'unknown'));
        }

        // 将所有文字块拼接，按行分隔
        return implode("\n", array_column($data['items'], 'text'));
    }

    /**
     * 压缩图片至微信 OCR 接口要求（≤4MB，最长边≤4096px）
     * 需要 PHP GD 或 Imagick 扩展（优先用 GD）
     *
     * @param  string $sourcePath  原始图片绝对路径
     * @return string              压缩后图片路径（若无需压缩则返回原路径）
     */
    private function compressImage(string $sourcePath): string
    {
        $maxBytes  = (int)(3.8 * 1024 * 1024);  // 3.8MB，留余量
        $maxPixels = 4096;

        // 文件已合规，直接返回
        $sizeInfo = getimagesize($sourcePath);
        $w = $sizeInfo ? $sizeInfo[0] : 0;
        $h = $sizeInfo ? $sizeInfo[1] : 0;
        if (filesize($sourcePath) <= $maxBytes && max($w, $h) <= $maxPixels) {
            return $sourcePath;
        }

        if (!extension_loaded('gd')) {
            Log::warning('GD 扩展未安装，跳过图片压缩，OCR 可能因图片过大失败');
            return $sourcePath;
        }

        $info = getimagesize($sourcePath);
        $mime = $info['mime'] ?? 'image/jpeg';

        switch ($mime) {
            case 'image/png':
                $src = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $src = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $src = function_exists('imagecreatefromwebp')
                    ? imagecreatefromwebp($sourcePath)
                    : imagecreatefromjpeg($sourcePath);
                break;
            default:
                $src = imagecreatefromjpeg($sourcePath);
                break;
        }

        if (!$src) {
            return $sourcePath;
        }

        [$origW, $origH] = [$info[0], $info[1]];

        // 计算缩放比例（确保最长边不超过 4096px）
        $scale = min(1.0, $maxPixels / max($origW, $origH));
        $newW  = (int) round($origW * $scale);
        $newH  = (int) round($origH * $scale);

        $dst = imagecreatetruecolor($newW, $newH);

        // PNG 透明背景处理
        if ($mime === 'image/png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagefilledrectangle($dst, 0, 0, $newW, $newH,
                imagecolorallocatealpha($dst, 255, 255, 255, 127));
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        // 逐步降低 JPEG quality 直到文件 ≤ 3.8MB
        $tmpPath = tempnam(sys_get_temp_dir(), 'ocr_') . '.jpg';
        $quality = 85;

        do {
            imagejpeg($dst, $tmpPath, $quality);
            $quality -= 5;
        } while ($quality >= 40 && filesize($tmpPath) > $maxBytes);

        imagedestroy($dst);

        return $tmpPath;
    }

    /**
     * code2session：用 wx.login() 的 code 换取 openid
     * （AuthController 中直接用 Http::get 调用，此处备用）
     */
    public function code2session(string $code): array
    {
        $res = Http::get('https://api.weixin.qq.com/sns/jscode2session', [
            'appid'      => $this->appId,
            'secret'     => $this->appSecret,
            'js_code'    => $code,
            'grant_type' => 'authorization_code',
        ]);

        return $res->json();
    }
}
