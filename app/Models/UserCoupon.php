<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $table = 'user_coupons';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'is_used',
        'is_expired',
        'used_at',
        'expired_at',
    ];

    protected $casts = [
        'is_used'    => 'integer',
        'is_expired' => 'integer',
        'used_at'    => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}