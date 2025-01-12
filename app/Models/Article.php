<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Article extends Model
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
    

    public function scopeWithKeyword(Builder $query, string $keyword): Builder
    {
        return $query->where('title', 'like', '%' . $keyword . '%')
            ->orWhere('description', 'like', '%' . $keyword . '%')
            ->orWhere('content', 'like', '%' . $keyword . '%');
    }

    public function scopeWithSourceName(Builder $query, string|array $sourceNames): Builder
    {
        if (is_array($sourceNames)) {
            return $query->whereHas('newsSource', function ($q) use ($sourceNames) {
                $q->whereIn('name', $sourceNames);
            });
        } else {
            return $query->whereHas('newsSource', function ($q) use ($sourceNames) {
                $q->where('name', 'like', '%' . $sourceNames . '%');
            });
        }
    }

    public function scopeWithCategoryName(Builder $query, string|array $categoryNames): Builder
    {
        if (is_array($categoryNames)) {
            return $query->whereHas('category', function ($q) use ($categoryNames) {
                $q->whereIn('name', 'like', '%' . $categoryNames . '%');
            });
        } else {
            return $query->whereHas('category', function ($q) use ($categoryNames) {
                $q->where('name', 'like', '%' . $categoryNames . '%');
            });
        }
    }
}
