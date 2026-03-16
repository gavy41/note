<?php
// app/Models/Card.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['user_id', 'type', 'content', 'source', 'author', 'color'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // API 输出时将时间转为时间戳（小程序端统一用 Unix timestamp）
    public function toArray()
    {
        $arr = parent::toArray();
        $arr['created_at'] = $this->created_at ? $this->created_at->timestamp : null;
        $arr['updated_at'] = $this->updated_at ? $this->updated_at->timestamp : null;
        return $arr;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
