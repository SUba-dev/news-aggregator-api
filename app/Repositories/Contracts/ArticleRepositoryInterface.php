<?php
namespace App\Repositories\Contracts;

use app\DTOs\NewsArticleDto;
use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function store( $articleDto): void;
    public function editOrCreate( $articleDto): void;

    public function getArticleById(int $id): ?Article;
    public function getAllArticle(): Collection;
    public function getAllArticleWithPagination(): LengthAwarePaginator;
    public function searchArticles(array $filters): Collection;

    public function getArticleBySourceId(int $sourceId): Collection;
    public function getArticleBySources(array $sources): Collection;

    public function getArticleByCategoryId(int $categoryId): Collection;
    public function getArticleByCategories(array $categories): Collection;

    public function getArticleByAuthors(string|array $authors): Collection;

    public function getArticleByPublishedAt(string $fromPublishedAt, string $toPublishedAt): Collection;

}