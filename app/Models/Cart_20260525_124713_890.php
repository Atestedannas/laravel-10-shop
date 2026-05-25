<?php

// 修复 Cart 模型字段映射
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'goods_id',
        'sku_id',
        'count',
        'selected',
    ];

    protected $casts = [
        'selected' => 'boolean',
        'count' => 'integer',
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function sku()
    {
        return $this->belongsTo(GoodsSku::class, 'sku_id');
    }
}