<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = ['openid', 'nickname', 'avatar', 'last_login_at'];

    protected $hidden = ['remember_token'];

    protected $casts = ['last_login_at' => 'datetime'];

    // JWT 接口必须实现
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
