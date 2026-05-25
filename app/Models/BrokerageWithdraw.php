<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerageWithdraw extends Model
{
    protected $table = 'brokerage_withdraws';

    protected $fillable = [
        'user_id',
        'order_no',
        'price',
        'service_fee',
        'real_price',
        'type',
        'bank_name',
        'bank_account',
        'bank_user',
        'status',
        'refuse_reason',
        'audit_time',
        'transfer_time',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'service_fee'  => 'decimal:2',
        'real_price'   => 'decimal:2',
        'type'         => 'integer',
        'status'       => 'integer',
        'audit_time'   => 'datetime',
        'transfer_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}