<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CombinationActivity extends Model
{
    use SoftDeletes;

    protected $table = 'combination_activities';

    protected $fillable = [
        'goods_id',
        'goods_sku_id',
        'group_price',
        'required_count',
        'limit_time',
        'stock',
        'virtual_sales',
        'limit_count',
        'start_time',
        'end_time',
        'status',
        'sort',
    ];

    protected $casts = [
        'group_price'    => 'decimal:2',
        'required_count' => 'integer',
        'limit_time'     => 'integer',
        'stock'          => 'integer',
        'virtual_sales'  => 'integer',
        'limit_count'    => 'integer',
        'status'         => 'integer',
        'sort'           => 'integer',
        'start_time'     => 'datetime',
        'end_time'       => 'datetime',
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function records()
    {
        return $this->hasMany(CombinationRecord::class, 'activity_id');
    }
}