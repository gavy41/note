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
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',   // 最大 10MB
        ]);

        $file = $request->file('image');
        $text = $this->wechat->ocrImage($file->getRealPath());

        return response()->json(['data' => ['text' => $text]]);
    }
}
