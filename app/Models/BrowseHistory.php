<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrowseHistory extends Model
{
    protected $fillable = ['user_id', 'spu_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'spu_id');
    }
}