<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    // API 路由已通过 routes/api.php 排除在 CSRF 之外
    protected $except = [
        'api/*',
    ];
}
