<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BargainActivity extends Model
{
    use SoftDeletes;

    protected $table = 'bargain_activities';

    protected $fillable = [
        'goods_id',
        'goods_sku_id',
        'origin_price',
        'min_price',
        'bargain_min',
        'bargain_max',
        'stock',
        'help_max',
        'self_bargain_max',
        'bargain_mode',
        'virtual_sales',
        'start_time',
        'end_time',
        'status',
        'sort',
    ];

    protected $casts = [
        'origin_price'     => 'decimal:2',
        'min_price'        => 'decimal:2',
        'bargain_min'      => 'decimal:2',
        'bargain_max'      => 'decimal:2',
        'stock'            => 'integer',
        'help_max'         => 'integer',
        'self_bargain_max' => 'integer',
        'bargain_mode'     => 'integer',
        'virtual_sales'    => 'integer',
        'status'           => 'integer',
        'sort'             => 'integer',
        'start_time'       => 'datetime',
        'end_time'         => 'datetime',
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function records()
    {
        return $this->hasMany(BargainRecord::class, 'activity_id');
    }
}