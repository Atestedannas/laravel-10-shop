<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceLog extends Model
{
    protected $table = 'balance_logs';

    protected $fillable = [
        'user_id',
        'scene',
        'money',
        'describe',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
