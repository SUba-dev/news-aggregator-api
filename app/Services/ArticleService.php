<?php

namespace App\Services;

use app\Repositories\EloquentArticleRepository;

class ArticleService
{
    private EloquentArticleRepository $articleRepo;

    public function __construct(EloquentArticleRepository $repository)
    {
        $this->articleRepo = $repository;
    }

    public function listAllArticles()
    {
        return $this->articleRepo->getAllArticle();
    }


    public function listAllArticlesWithPagination($perPage, $page)
    {
        return $this->articleRepo->getAllArticleWithPagination($perPage, $page);
    }

    


    public function viewArticle($id)
    {
        return $this->articleRepo->getArticleById($id);
    }


    public function searchArticles($query)
    {
        return $this->articleRepo->searchArticles($query);
    }
}
