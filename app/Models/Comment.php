<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'goods_id',
        'order_id',
        'order_goods_id',
        'score',
        'content',
        'images',
        'status',
    ];

    protected $casts = [
        'score'  => 'integer',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}