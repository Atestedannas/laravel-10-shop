<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSocialUser extends Model
{
    protected $table = 'member_social_users';

    protected $fillable = [
        'user_id',
        'type',
        'openid',
        'unionid',
        'nickname',
        'avatar',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}