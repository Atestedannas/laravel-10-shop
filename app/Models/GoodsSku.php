<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsSku extends Model
{
    protected $table = 'goods_skus';

    protected $fillable = [
        'goods_id',
        'sku_spec_ids',
        'goods_price',
        'line_price',
        'stock',
        'goods_weight',
        'goods_no',
    ];

    protected $casts = [
        'goods_price'  => 'decimal:2',
        'line_price'   => 'decimal:2',
        'goods_weight' => 'decimal:2',
        'stock'        => 'integer',
    ];
}