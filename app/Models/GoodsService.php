<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsService extends Model
{
    protected $table = 'goods_services';

    protected $fillable = [
        'goods_id',
        'service_name',
    ];
}