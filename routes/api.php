<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\OcrController;
use App\Http\Controllers\Api\FeedbackController;
/*
|--------------------------------------------------------------------------
| 小程序 API 路由
| 前缀: /api  (在 app/Http/Kernel.php 的 api 中间件组中)
| 认证: JWT Bearer Token
|--------------------------------------------------------------------------
*/

// 公开路由（不需要 token）
Route::post('/auth/login', [AuthController::class, 'login']);

// 需要 JWT 认证的路由
Route::middleware('api')->group(function () {//auth:

    // 碎片 CRUD
    Route::get('/feedback',        [FeedbackController::class, 'store']);
    Route::get('/cards',           [CardController::class, 'index']);   // 列表/按日期筛选
    Route::post('/cards',          [CardController::class, 'store']);   // 新增
    Route::get('/cards/date-set',  [CardController::class, 'dateSet']); // 有碎片的日期集合
    Route::get('/cards/{id}',      [CardController::class, 'show']);    // 详情
    Route::put('/cards/{id}',      [CardController::class, 'update']);  // 更新
    Route::delete('/cards/{id}',   [CardController::class, 'destroy']); // 删除

    // OCR 识别
    Route::post('/ocr/upload', [OcrController::class, 'upload']);
});
