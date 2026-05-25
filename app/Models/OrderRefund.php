<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRefund extends Model
{
    protected $table = 'order_refunds';

    protected $fillable = [
        'user_id',
        'order_id',
        'order_goods_id',
        'refund_type',
        'refund_status',
        'amount',
        'content',
        'images',
        'audit_status',
        'express_no',
        'express_company',
        'completed_at',
    ];

    protected $casts = [
        'refund_type'  => 'integer',
        'refund_status' => 'integer',
        'audit_status'  => 'integer',
        'amount'        => 'decimal:2',
    ];
}