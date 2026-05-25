<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardActivity extends Model
{
    use SoftDeletes;

    protected $table = 'reward_activities';

    protected $fillable = [
        'name',
        'type',
        'threshold_price',
        'discount_price',
        'gift_goods_id',
        'gift_goods_sku_id',
        'gift_count',
        'scope_type',
        'scope_value',
        'start_time',
        'end_time',
        'status',
        'sort',
    ];

    protected $casts = [
        'type'           => 'integer',
        'threshold_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'gift_count'     => 'integer',
        'scope_type'     => 'integer',
        'scope_value'    => 'array',
        'status'         => 'integer',
        'sort'           => 'integer',
        'start_time'     => 'datetime',
        'end_time'       => 'datetime',
    ];

    public function giftGoods()
    {
        return $this->belongsTo(Goods::class, 'gift_goods_id');
    }
}