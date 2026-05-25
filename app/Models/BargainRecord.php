<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BargainRecord extends Model
{
    protected $table = 'bargain_records';

    protected $fillable = [
        'activity_id',
        'user_id',
        'order_id',
        'origin_price',
        'current_price',
        'bargain_total',
        'help_count',
        'status',
        'expire_time',
        'success_time',
    ];

    protected $casts = [
        'origin_price'  => 'decimal:2',
        'current_price' => 'decimal:2',
        'bargain_total' => 'decimal:2',
        'help_count'    => 'integer',
        'status'        => 'integer',
        'expire_time'   => 'datetime',
        'success_time'  => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(BargainActivity::class, 'activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}