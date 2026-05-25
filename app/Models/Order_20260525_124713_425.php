<?php

// 修复 Order 模型字段映射（适配 uniapp 前端）
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_no',
        'user_id',
        'total_price',
        'freight_price',
        'order_price',
        'status',
        'remark',
        'coupon_id',
        'cancel_reason',
    ];

    protected $casts = [
        'status' => 'integer',
        'total_price' => 'decimal:2',
        'freight_price' => 'decimal:2',
        'order_price' => 'decimal:2',
    ];

    public function goods()
    {
        return $this->hasMany(OrderGoods::class);
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}