<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $table = 'order_address';

    protected $fillable = [
        'order_id',
        'name',
        'phone',
        'province',
        'city',
        'region',
        'detail',
    ];
}