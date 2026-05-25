<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointActivity extends Model
{
    use SoftDeletes;

    protected $table = 'point_activities';

    protected $fillable = [
        'goods_id',
        'goods_sku_id',
        'point_price',
        'stock',
        'sold_count',
        'limit_count',
        'origin_price',
        'image',
        'start_time',
        'end_time',
        'status',
        'sort',
    ];

    protected $casts = [
        'point_price' => 'integer',
        'stock'       => 'integer',
        'sold_count'  => 'integer',
        'limit_count' => 'integer',
        'origin_price' => 'decimal:2',
        'status'      => 'integer',
        'sort'        => 'integer',
        'start_time'  => 'datetime',
        'end_time'    => 'datetime',
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }
}