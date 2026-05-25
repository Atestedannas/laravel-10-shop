<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsSpecValue extends Model
{
    protected $table = 'goods_spec_values';

    protected $fillable = [
        'goods_spec_id',
        'spec_value',
        'sort',
    ];
}