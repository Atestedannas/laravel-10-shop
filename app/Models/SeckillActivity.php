<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeckillActivity extends Model
{
    use SoftDeletes;

    protected $table = 'seckill_activities';

    protected $fillable = [
        'config_id',
        'goods_id',
        'goods_sku_id',
        'seckill_price',
        'seckill_stock',
        'sold_count',
        'limit_count',
        'origin_price',
        'start_date',
        'status',
        'sort',
    ];

    protected $casts = [
        'seckill_price' => 'decimal:2',
        'seckill_stock' => 'integer',
        'sold_count'    => 'integer',
        'limit_count'   => 'integer',
        'origin_price'  => 'decimal:2',
        'status'        => 'integer',
        'sort'          => 'integer',
        'start_date'    => 'date',
    ];

    public function config()
    {
        return $this->belongsTo(SeckillConfig::class, 'config_id');
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }
}