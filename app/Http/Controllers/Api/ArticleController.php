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
     * @OA\Get(
     *     path="/api/articles/list",
     *     summary="List Articles",
     *     description="Get a list of articles.",
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of articles per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/Resources/ArticleCollection")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No articles found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve article."
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/articles/show",
     *     summary="View Article",
     *     description="View details of a specific article.",
     *     security={{"bearerAuth": {}}}, 
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID of the article",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         @OA\JsonContent(ref="#/Resources/ArticleResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve article."
     *     )
     * )
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


    /**
     * @OA\Get(
     *     path="/api/articles/search",
     *     summary="Search for articles",
     *     description="Search articles based on various criteria.",
     *     security={{"bearerAuth": {}}}, 
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search for in articles",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         description="Start date for the search (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         description="End date for the search (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Category of articles (e.g., 'technology', 'sports')",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Source of articles (e.g., 'The New York Times')",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of articles per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/Resources/ArticleCollection")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No articles found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve article."
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/articles/user-preferences",
     *     summary="Get Personalized Articles",
     *     description="Retrieves a list of articles personalized for the authenticated user based on their preferences.",
     *     security={{"bearerAuth": {}}}, 
     *     @OA\Response(
     *         response=200,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/Resources/ArticleCollection")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User preferences not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve personalized news feed."
     *     )
     * )
     */
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
