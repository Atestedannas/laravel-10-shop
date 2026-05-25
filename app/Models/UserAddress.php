<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'province_id',
        'city_id',
        'region_id',
        'detail',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'integer',
    ];
}