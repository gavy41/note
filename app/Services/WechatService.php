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
     * 微信 OCR API 文档:
     * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/img-ocr/ocr/printedTextOCR.html
     *
     * @param  string $imagePath  本地图片绝对路径
     * @return string             识别出的文字（多行用 \n 分隔）
     */
    public function ocrImage(string $imagePath): string
    {
        $token = $this->getAccessToken();

        // 使用 img_url 方式需要图片可公网访问；这里改用 multipart 直接上传文件
        $res = Http::withToken($token, 'Bearer')
            ->attach('img', file_get_contents($imagePath), 'image.jpg')
            ->post("https://api.weixin.qq.com/cv/ocr/comm?access_token={$token}");

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
