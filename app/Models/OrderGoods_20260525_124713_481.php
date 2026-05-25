<?php

// 修复 OrderGoods 模型字段映射
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $table = 'order_goods';

    protected $fillable = [
        'order_id',
        'goods_id',
        'sku_id',
        'goods_name',
        'sku_text',
        'price',
        'count',
        'pic_url',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'count' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}