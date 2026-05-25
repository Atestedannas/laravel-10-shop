<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsImage extends Model
{
    protected $table = 'goods_images';

    protected $fillable = [
        'goods_id',
        'image_url',
        'sort',
    ];
}