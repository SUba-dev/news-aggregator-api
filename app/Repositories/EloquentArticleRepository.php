<?php

namespace app\Repositories;

use app\DTOs\NewsArticleDto;
use ArticleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class EloquentArticleRepository implements ArticleRepositoryInterface
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function store(NewsArticleDto $articleDto): Model
    {
        $article = $this->model->create([
            'source'      => $articleDto->source,
            'news_source_id'      => $articleDto->sourceId,
            'category_id'      => $articleDto->categoryId,
            'title'       => $articleDto->title,
            'author'       => $articleDto->author,
            'description' => $articleDto->description,
            'content' => $articleDto->content,
            'url'         => $articleDto->url,
            'published_at'   => $articleDto->publishedAt,
        ]);

        return $article;
    }



    public function updateOrCreate(NewsArticleDto $articleDto): Model
    {
        $article = $this->model->updateOrCreate([
            'source'      => $articleDto->source,
            'news_source_id'      => $articleDto->sourceId,
            'category_id'      => $articleDto->categoryId,
            'title'       => $articleDto->title,
            'author'       => $articleDto->author,
            'description' => $articleDto->description,
            'content' => $articleDto->content,
            'url'         => $articleDto->url,
            'published_at'   => $articleDto->publishedAt,
        ]);

        return $article;
    }
}
