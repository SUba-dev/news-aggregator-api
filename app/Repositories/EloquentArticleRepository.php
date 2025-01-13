<?php

namespace app\Repositories;

use app\DTOs\NewsArticleDto;
use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentArticleRepository implements ArticleRepositoryInterface
{
    private $model;

    public function __construct()
    {
        //$this->model = $model;
    }

    /**
     * Retrieves all articles from the database.
     *
     * @return Collection 
     */
    public function getAllArticle(): Collection
    {
        return Article::all();
    }

    /**
     * Retrieves all articles with pagination.
     *
     * @param int $perPage Number of articles per page
     * @param int $page Page number
     * @return LengthAwarePaginator
     */
    public function getAllArticleWithPagination(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return Article::paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Searches for articles based on given filters.
     *
     * @param array $filters Array of filters (e.g., 'keyword', 'fromDate', 'toDate', 'source', 'category')
     * @return Collection
     */
    public function searchArticles(array $filters): Collection
    {
        $query = Article::query();
        $this->filterbyData($query, $filters);
        return $query->get();
    }


    /**
     * Retrieves personalized articles based on user preferences.
     *
     * @param array $filters Array of filters (e.g., 'preferred_sources', 'preferred_categories', 'preferred_authors')
     * @return Collection
     */
    public function getPersonalizedArticle(array $filters): Collection
    {
        $query = Article::query();
        $this->filterbyPreference($query, $filters);
        return $query->get();
    }


    /**
     * Retrieves an article by its ID.
     *
     * @param int $id Article ID
     * @return Article|null
     */
    public function getArticleById(int $id): ?Article
    {
        return Article::find($id);
    }


    /**
     * Retrieves articles by source ID.
     *
     * @param int $sourceId Source ID
     * @return Collection
     */
    public function getArticleBySourceId(int $sourceId): Collection
    {
        return Article::where('news_source_id', $sourceId)->get();
    }


    /**
     * Retrieves articles by an array of source name.
     *
     * @param array $sources Array of source name
     * @return Collection
     */
    public function getArticleBySources(array $sources): Collection
    {
        return Article::withSourceName($sources)->get();
    }


    /**
     * Retrieves articles by category ID.
     *
     * @param int $categoryId Category ID
     * @return Collection
     */
    public function getArticleByCategoryId(int $categoryId): Collection
    {
        return Article::where('category_id', $categoryId)->get();
    }

    /**
     * Retrieves articles by an array of category name.
     *
     * @param array $categories Array of category names
     * @return Collection
     */
    public function getArticleByCategories(array $categories): Collection
    {
        return Article::withCategoryName($categories)->get();
    }


    /**
     * Retrieves articles by author(s).
     *
     * @param string|array $authors Author name(s)
     * @return Collection
     */
    public function getArticleByAuthors(string|array $authors): Collection
    {
        $query = Article::query();
        if (is_array($authors)) {
            $query->whereIn('author', 'like', '%' . $authors . '%');
        } else {
            $query->where('author', 'like', '%' . $authors . '%');
        }
        return $query->get();
    }




    public function getArticleByPublishedAt(string $fromPublishedAt, string $toPublishedAt): Collection
    {
        return Article::whereDate('published_at', '>=', Carbon::createFromFormat('Y-m-d', $fromPublishedAt)->startOfDay())
            ->whereDate('published_at', '<=', Carbon::createFromFormat('Y-m-d', $toPublishedAt)->endOfDay())->get();
    }



    /**
     * Filters articles based on various criteria.
     *
     * @param Builder $query Query builder instance
     * @param array $filters Array of filters
     * @return Builder
     */
    private function filterbyData(Builder $query, array $filters)
    {
        foreach ($filters as $filter => $value) {
            $query = match ($filter) {
                'keyword' => $query->withKeyword($value),
                'fromDate' => $query->whereDate('published_at', '>=', Carbon::createFromFormat('Y-m-d', $value)->startOfDay()),
                'toDate' => $query->whereDate('published_at', '<=', Carbon::createFromFormat('Y-m-d', $value)->endOfDay()),
                'source' => $query->withSourceName($value),
                'category' => $query->withCategoryName($value),
                default => $query,
            };
        }
    }


    /**
     * Filters articles based on user preferences.
     *
     * @param Builder $query Query builder instance
     * @param array $filters Array of filters (e.g., 'preferred_sources', 'preferred_categories', 'preferred_authors')
     * @return Builder
     */
    private function filterbyPreference(Builder $query, array $filters)
    {
        foreach ($filters as $filter => $value) {
            if (!empty($value)) {
                $query = match ($filter) {
                    'preferred_sources' => $query->whereIn('news_source_id', $value),
                    'preferred_categories' => $query->whereIn('category_id', $value),
                    'preferred_authors' => $query->whereIn('author', $value),
                    default => $query,
                };
            }
        }
    }

    public function store($articleDto): void
    {
        // $article = $this->model->create([
        //     'source'      => $articleDto->source,
        //     'news_source_id'      => $articleDto->sourceId,
        //     'category_id'      => $articleDto->categoryId,
        //     'title'       => $articleDto->title,
        //     'author'       => $articleDto->author,
        //     'description' => $articleDto->description,
        //     'content' => $articleDto->content,
        //     'url'         => $articleDto->url,
        //     'published_at'   => $articleDto->publishedAt,
        // ]);

        // return $article;
    }



    public function editOrCreate($articleDto): void
    // public function editOrCreate(NewsArticleDto $articleDto): Model
    {
        // $article = $this->model->updateOrCreate([
        //     'source'      => $articleDto->source,
        //     'news_source_id'      => $articleDto->sourceId,
        //     'category_id'      => $articleDto->categoryId,
        //     'title'       => $articleDto->title,
        //     'author'       => $articleDto->author,
        //     'description' => $articleDto->description,
        //     'content' => $articleDto->content,
        //     'url'         => $articleDto->url,
        //     'published_at'   => $articleDto->publishedAt,
        // ]);

        // return $article;
    }
}
