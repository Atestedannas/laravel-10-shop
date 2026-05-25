<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSignInRecord extends Model
{
    protected $table = 'member_sign_in_records';

    protected $fillable = [
        'user_id',
        'sign_date',
        'point',
        'day',
    ];

    protected $casts = [
        'sign_date' => 'date',
        'point'     => 'integer',
        'day'       => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}