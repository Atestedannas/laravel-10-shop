<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsSpec extends Model
{
    protected $table = 'goods_specs';

    protected $fillable = [
        'goods_id',
        'spec_name',
        'sort',
    ];

    public function values()
    {
        return $this->hasMany(GoodsSpecValue::class);
    }
}