<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Help extends Model
{
    protected $table = 'helps';

    protected $fillable = [
        'title',
        'content',
        'sort',
        'status',
    ];
}