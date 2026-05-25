<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberAddress extends Model
{
    protected $table = 'member_addresses';

    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'province_id',
        'city_id',
        'district_id',
        'province',
        'city',
        'district',
        'detail',
        'is_default',
    ];

    protected $casts = [
        'is_default'    => 'boolean',
        'province_id'   => 'integer',
        'city_id'       => 'integer',
        'district_id'   => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}