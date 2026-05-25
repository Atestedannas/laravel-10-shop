<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'cover',
        'sort',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class);
    }
}