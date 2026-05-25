<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        'phone',
        'avatar',
        'nickname',
        'balance',
        'points',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'points'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}