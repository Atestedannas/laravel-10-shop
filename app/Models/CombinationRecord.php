<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CombinationRecord extends Model
{
    protected $table = 'combination_records';

    protected $fillable = [
        'activity_id',
        'user_id',
        'order_id',
        'group_no',
        'required_count',
        'current_count',
        'status',
        'expire_time',
        'success_time',
    ];

    protected $casts = [
        'required_count' => 'integer',
        'current_count'  => 'integer',
        'status'         => 'integer',
        'expire_time'    => 'datetime',
        'success_time'   => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(CombinationActivity::class, 'activity_id');
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