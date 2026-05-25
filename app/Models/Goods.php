<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model
{
    use SoftDeletes;

    protected $table = 'goods';

    protected $fillable = [
        'category_id',
        'goods_name',
        'goods_image',
        'selling_point',
        'spec_type',
        'content',
        'goods_price_min',
        'line_price_min',
        'goods_sales',
        'goods_status',
        'sort',
        'video',
        'video_cover',
        'is_user_grade',
    ];

    protected $casts = [
        'spec_type'       => 'integer',
        'goods_status'    => 'integer',
        'sort'            => 'integer',
        'is_user_grade'   => 'integer',
        'goods_price_min' => 'decimal:2',
        'line_price_min'  => 'decimal:2',
    ];

    public function images()
    {
        return $this->hasMany(GoodsImage::class);
    }

    public function specs()
    {
        return $this->hasMany(GoodsSpec::class);
    }

    public function skus()
    {
        return $this->hasMany(GoodsSku::class);
    }

    public function services()
    {
        return $this->hasMany(GoodsService::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}