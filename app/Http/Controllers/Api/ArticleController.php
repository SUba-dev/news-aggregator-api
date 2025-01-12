<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleSearchRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use app\Repositories\EloquentArticleRepository;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    private EloquentArticleRepository $articleRepo;

    public function __construct(EloquentArticleRepository $repo)
    {
        $this->articleRepo = $repo;
    }



    /** 
     * List all articles
     */
    public function list(Request $request)
    {
        try {
            if ($request->has('perPage') || $request->has('page')) {
                $perPage = $request->input('perPage', 10);
                $page = $request->input('page', 1);
                $articles = $this->articleRepo->getAllArticleWithPagination($perPage, $page);
            } else {
                $articles = $this->articleRepo->getAllArticle();
            }
            if (!$articles) {
                return ApiResponse::notFound();
            }
            return ApiResponse::success(new ArticleCollection($articles), Response::HTTP_OK);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve articles.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** 
     * View article
     */
    public function show(Request $request)
    {
        $id = trim($request->input('id'));
        try {
            $article = $this->articleRepo->getArticleById($id);
            if (!$article) {
                return ApiResponse::notFound();
            }
            return  ApiResponse::success(new ArticleResource($article), Response::HTTP_OK);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve article.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function search(ArticleSearchRequest $request)
    {
        $inputFilter = $request->only(['keyword', 'fromDate', 'toDate', 'category', 'source', 'perPage', 'page']);

        $filters = collect($inputFilter)->filter(function ($value) {
            return !is_null($value) && !empty($value);
        })->toArray();

        try {
            $articles = $this->articleRepo->searchArticles($filters);
            if (!$articles) {
                return ApiResponse::notFound();
            }
            return  ApiResponse::success(new ArticleCollection($articles), Response::HTTP_OK);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return ApiResponse::error('Failed to retrieve article.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function personalizedArticle(Request $request)
    {
        try {
            $user = $request->user();
            $userPreferences = $user->preferences;
            if (!$userPreferences) {
                return ApiResponse::notFound('User preferences not found.');
            }
            $filters = [
                'preferred_sources' => array_values($user->preferences->preferred_sources),
                'preferred_categories' => array_values($user->preferences->preferred_categories),
                'preferred_authors' => array_values($user->preferences->preferred_authors),
            ];
            $articles = $this->articleRepo->getPersonalizedArticle($filters);
            return ApiResponse::success(new ArticleCollection($articles), Response::HTTP_OK);
        } catch (\Exception $e) {
            // print_r($e->getMessage());
            return ApiResponse::error('Failed to retrieve personalized news feed.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
