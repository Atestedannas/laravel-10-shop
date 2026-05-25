<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeckillConfig extends Model
{
    protected $table = 'seckill_configs';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'status',
        'sort',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort'   => 'integer',
    ];

    public function activities()
    {
        return $this->hasMany(SeckillActivity::class, 'config_id');
    }
}