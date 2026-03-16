<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| 管理后台路由
| 前缀: /admin
| 认证: Session（普通 Web 登录）
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect('/admin'));

Route::prefix('admin')->name('admin.')->group(function () {

    // 登录/登出
    Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout',[AdminAuthController::class, 'logout'])->name('logout');

    // 需要登录
    Route::middleware('admin.auth')->group(function () {
        Route::get('/',               [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/cards',          [CardController::class, 'index'])->name('cards.index');
        Route::delete('/cards/{id}',  [CardController::class, 'destroy'])->name('cards.destroy');
        Route::get('/users',          [UserController::class, 'index'])->name('users.index');
        Route::delete('/users/{id}',  [UserController::class, 'destroy'])->name('users.destroy');
    });
});
