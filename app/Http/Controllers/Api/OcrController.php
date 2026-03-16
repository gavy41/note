<?php
// app/Http/Controllers/Api/OcrController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WechatService;
use Illuminate\Http\Request;

class OcrController extends Controller
{
    protected WechatService $wechat;

    public function __construct(WechatService $wechat)
    {
        $this->wechat = $wechat;
    }

    /**
     * POST /api/ocr/upload
     * multipart/form-data: image=<file>
     * 响应: { data: { text: "识别结果" } }
     *
     * 注意：服务端会在调用微信 OCR 前自动压缩图片到 ≤4MB / ≤4096px
     * 接口本身接受最大 20MB 的上传（客户端原图）
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:20480',   // 接受最大 20MB，服务端压缩后再发给微信
        ]);

        $file = $request->file('image');

        try {
            $text = $this->wechat->ocrImage($file->getRealPath());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['data' => ['text' => $text]]);
    }
}
