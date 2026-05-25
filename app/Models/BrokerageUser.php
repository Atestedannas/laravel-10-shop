<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerageUser extends Model
{
    protected $table = 'brokerage_users';

    protected $fillable = [
        'user_id',
        'parent_id',
        'level',
        'brokerage_price',
        'frozen_price',
        'total_brokerage_price',
        'total_withdraw_price',
        'user_count',
        'order_count',
        'status',
        'apply_time',
        'audit_time',
    ];

    protected $casts = [
        'level'                  => 'integer',
        'brokerage_price'        => 'decimal:2',
        'frozen_price'           => 'decimal:2',
        'total_brokerage_price'  => 'decimal:2',
        'total_withdraw_price'   => 'decimal:2',
        'user_count'             => 'integer',
        'order_count'            => 'integer',
        'status'                 => 'integer',
        'apply_time'             => 'datetime',
        'audit_time'             => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(BrokerageUser::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BrokerageUser::class, 'parent_id');
    }

    public function records()
    {
        return $this->hasMany(BrokerageRecord::class, 'user_id');
    }

    public function withdraws()
    {
        return $this->hasMany(BrokerageWithdraw::class, 'user_id');
    }
}