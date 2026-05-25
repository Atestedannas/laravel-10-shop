<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $table = 'recharges';

    protected $fillable = [
        'user_id',
        'order_no',
        'plan_id',
        'money',
        'pay_status',
        'pay_method',
        'pay_time',
    ];

    protected $casts = [
        'pay_status' => 'integer',
        'money'      => 'decimal:2',
    ];
}