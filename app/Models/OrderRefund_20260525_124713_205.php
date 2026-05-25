<?php

// 修复 OrderRefund 模型字段映射
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRefund extends Model
{
    protected $table = 'order_refunds';

    protected $fillable = [
        'refund_no',
        'user_id',
        'order_id',
        'order_item_id',
        'refund_type',
        'refund_amount',
        'refund_reason',
        'refund_desc',
        'pics',
        'status',
        'express_company',
        'express_no',
    ];

    protected $casts = [
        'refund_type' => 'integer',
        'status' => 'integer',
        'refund_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderGoods()
    {
        return $this->belongsTo(OrderGoods::class, 'order_item_id');
    }
}