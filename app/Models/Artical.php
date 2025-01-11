<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artical extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'news_source_id',
        'category_id',
        'title',
        'author',
        'description',
        'content',
        'url',
        'published_at',
    ];

    public function newsSource()
    {
        return $this->belongsTo(NewsSource::class, 'news_source_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
