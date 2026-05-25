<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPointRecord extends Model
{
    protected $table = 'member_point_records';

    protected $fillable = [
        'user_id',
        'title',
        'point',
        'total_point',
        'add_status',
    ];

    protected $casts = [
        'point'       => 'integer',
        'total_point' => 'integer',
        'add_status'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}