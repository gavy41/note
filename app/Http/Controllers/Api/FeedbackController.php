<?php
// app/Http/Controllers/Api/FeedbackController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * POST /api/feedback
     * body: { content: string, contact?: string }
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'contact' => 'nullable|string|max:128',
        ]);

        // 1. 存入数据库
        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'contact' => $request->contact ?? null,
        ]);

        // 2. 发送邮件通知
        $this->sendEmail($feedback);

        return response()->json(['data' => null], 200);
    }

    private function sendEmail(Feedback $feedback)
    {
        $user    = Auth::user();
        $openid  = $user ? substr($user->openid, -8) : '未知';
        $contact = $feedback->contact ?: '未填写';
        $time    = $feedback->created_at->setTimezone('Asia/Shanghai')->format('Y-m-d H:i:s');

        $subject = '【碎片灵感抽屉】新的用户反馈';

        $body = "您好，\n\n收到一条新的用户反馈：\n\n"
              . "━━━━━━━━━━━━━━━━━━━━\n"
              . "反馈内容：\n{$feedback->content}\n\n"
              . "联系方式：{$contact}\n"
              . "用户 OpenID（后8位）：{$openid}\n"
              . "提交时间：{$time}\n"
              . "━━━━━━━━━━━━━━━━━━━━\n\n"
              . "此邮件由系统自动发送，请勿回复。";

        try {
            Mail::raw($body, function ($message) use ($subject) {
                $message->to('454009906@qq.com')
                        ->subject($subject);
            });
        } catch (\Exception $e) {
            // 邮件发送失败不影响接口返回，记录日志即可
            Log::error('反馈邮件发送失败', [
                'feedback_id' => $feedback->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
