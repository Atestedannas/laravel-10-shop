<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerageRecord extends Model
{
    protected $table = 'brokerage_records';

    protected $fillable = [
        'user_id',
        'order_id',
        'order_goods_id',
        'source_user_id',
        'price',
        'brokerage_rate',
        'type',
        'status',
        'settle_time',
        'remark',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'brokerage_rate'  => 'decimal:2',
        'type'            => 'integer',
        'status'          => 'integer',
        'settle_time'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }
}