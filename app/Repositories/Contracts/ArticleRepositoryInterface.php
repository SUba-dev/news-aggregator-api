<?php

use app\DTOs\NewsArticleDto;
use Illuminate\Database\Eloquent\Model;

interface ArticleRepositoryInterface
{
    public function store(NewsArticleDto $articleDto): Model; 
}