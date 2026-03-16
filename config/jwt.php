<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT 密钥
    | 运行 php artisan jwt:secret 自动生成并写入 .env
    |--------------------------------------------------------------------------
    */
    'secret' => env('JWT_SECRET'),

    'keys' => [
        'public'     => env('JWT_PUBLIC_KEY'),
        'private'    => env('JWT_PRIVATE_KEY'),
        'passphrase' => env('JWT_PASSPHRASE'),
    ],

    // Token 有效期（分钟）。默认 20160 = 14 天
    'ttl' => env('JWT_TTL', 20160),

    // 刷新 TTL（分钟）。默认 20160 = 14 天
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),

    // 签名算法
    'algo' => env('JWT_ALGO', Tymon\JWTAuth\Providers\JWT\Provider::ALGO_HS256),

    // 需要验证的声明
    'required_claims' => ['iss', 'iat', 'exp', 'nbf', 'sub', 'jti'],

    'persistent_claims' => [],

    'lock_subject' => true,

    'leeway' => env('JWT_LEEWAY', 0),

    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    'decrypt_cookies' => false,

    'algorithm' => 'HS256',

    'providers' => [
        'jwt'          => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,
        'auth'         => Tymon\JWTAuth\Providers\Auth\Illuminate::class,
        'storage'      => Tymon\JWTAuth\Providers\Storage\Illuminate::class,
    ],
];
