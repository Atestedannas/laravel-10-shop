<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $table = 'order_goods';

    protected $fillable = [
        'order_id',
        'goods_id',
        'goods_sku_id',
        'goods_name',
        'goods_image',
        'goods_price',
        'total_num',
        'goods_props',
        'is_user_grade',
    ];

    protected $casts = [
        'goods_price'   => 'decimal:2',
        'total_num'     => 'integer',
        'is_user_grade' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}