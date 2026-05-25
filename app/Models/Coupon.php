<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'name',
        'coupon_type',
        'reduce_price',
        'discount',
        'min_price',
        'expire_type',
        'expire_day',
        'start_time',
        'end_time',
        'describe',
        'apply_range',
        'total_num',
        'receive_num',
        'sort',
        'status',
    ];

    protected $casts = [
        'coupon_type'  => 'integer',
        'expire_type'  => 'integer',
        'apply_range'  => 'integer',
        'total_num'    => 'integer',
        'receive_num'  => 'integer',
        'sort'         => 'integer',
        'status'       => 'integer',
        'reduce_price' => 'decimal:2',
        'min_price'    => 'decimal:2',
    ];
}