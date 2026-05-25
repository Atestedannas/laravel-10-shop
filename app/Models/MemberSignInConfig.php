<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberSignInConfig extends Model
{
    protected $table = 'member_sign_in_configs';

    protected $fillable = [
        'day',
        'point',
    ];

    protected $casts = [
        'day'   => 'integer',
        'point' => 'integer',
    ];
}