<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * 小程序登录
     * POST /api/auth/login
     * body: { code: "wx.login() 返回的 code" }
     */
    public function login(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        // 1. 用 code 换取 openid（调用微信服务端 API）
        $res = Http::get('https://api.weixin.qq.com/sns/jscode2session', [
            'appid'      => config('wechat.app_id'),
            'secret'     => config('wechat.app_secret'),
            'js_code'    => $request->code,
            'grant_type' => 'authorization_code',
        ]);

        $data = $res->json();

        if (isset($data['errcode']) && $data['errcode'] !== 0) {
            return response()->json([
                'message' => '微信登录失败：' . ($data['errmsg'] ?? 'unknown'),
            ], 400);
        }

        $openid = $data['openid'];

        // 2. 查找或创建用户
        $user = User::firstOrCreate(
            ['openid' => $openid],
            ['nickname' => '用户' . substr($openid, -6)]
        );

        $user->update(['last_login_at' => now()]);

        // 3. 签发 JWT
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'data' => [
                'token'  => $token,
                'openid' => $openid,
            ],
        ]);
    }
}
