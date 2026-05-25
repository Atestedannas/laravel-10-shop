<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'order_no',
        'order_status',
        'pay_status',
        'delivery_status',
        'receipt_status',
        'order_type',
        'delivery_type',
        'total_price',
        'coupon_money',
        'points_money',
        'express_price',
        'pay_price',
        'buyer_remark',
        'coupon_id',
        'is_use_points',
        'address_id',
        'create_time',
    ];

    protected $casts = [
        'order_status'    => 'integer',
        'pay_status'      => 'integer',
        'delivery_status' => 'integer',
        'receipt_status'  => 'integer',
        'order_type'      => 'integer',
        'delivery_type'   => 'integer',
        'is_use_points'   => 'integer',
        'total_price'     => 'decimal:2',
        'coupon_money'    => 'decimal:2',
        'points_money'    => 'decimal:2',
        'express_price'   => 'decimal:2',
        'pay_price'       => 'decimal:2',
    ];

    public function goods()
    {
        return $this->hasMany(OrderGoods::class);
    }

    public function address()
    {
        return $this->belongsTo(OrderAddress::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}