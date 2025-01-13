<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreferenceRequest;
use app\Repositories\EloquentUserPreferenceRepository;
use App\Services\ArticleService;
use App\Services\UserPreferenceService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Http\Resources\UserPreferencesResource;

class UserPreferenceController extends Controller
{
    private EloquentUserPreferenceRepository $userPreferenceRepo;

    public function __construct(EloquentUserPreferenceRepository $service)
    {
        $this->userPreferenceRepo = $service;
    }

    PHP

    <?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Requests\UserPreferenceRequest;
    use App\Http\Resources\ApiResponse;
    use App\Http\Resources\UserPreferencesResource;
    use App\Repositories\UserPreferenceRepositoryInterface;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    
    class UserPreferenceController extends Controller
    {
        private UserPreferenceRepositoryInterface $userPreferenceRepo;
    
        public function __construct(UserPreferenceRepositoryInterface $userPreferenceRepo)
        {
            $this->userPreferenceRepo = $userPreferenceRepo;
        }
    
        /**
         * @OA\Post(
         *     path="/api/user/preferences",
         *     summary="Store User Preferences",
         *     description="Store user preferences for personalized news recommendations.",
         *     security={{"bearerAuth": {}}}, 
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             @OA\Property(
         *                 property="preferred_sources",
         *                 type="array",
         *                 @OA\Items(type="string"),
         *                 nullable=true,
         *                 example=["The New York Times", "BBC News"]
         *             ),
         *             @OA\Property(
         *                 property="preferred_categories",
         *                 type="array",
         *                 @OA\Items(type="string"),
         *                 nullable=true,
         *                 example=["technology", "sports"]
         *             ),
         *             @OA\Property(
         *                 property="preferred_authors",
         *                 type="array",
         *                 @OA\Items(type="string"),
         *                 nullable=true,
         *                 example=["Doe", "Smith"]
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         @OA\JsonContent(ref="#/Resources/UserPreferencesResource")
         *     ),
         *     @OA\Response(
         *         response=422,
         *         description="Validation errors"
         *     ),
         *     @OA\Response(
         *         response=500,
         *         description="Internal server error"
         *     )
         * )
         */
    public function store(UserPreferenceRequest $request): JsonResponse
    {
        $user = $request->user();
        $this->userPreferenceRepo->store($user,  $request->all());

        return ApiResponse::success(new UserPreferencesResource($user->fresh()->preferences), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/user/preferences",
     *     summary="Get User Preferences",
     *     description="Retrieve the current user's preferences.",
     *     security={{"bearerAuth": {}}}, 
     *     @OA\Response(
     *         response=200,
     *         @OA\JsonContent(ref="#/Resources/UserPreferencesResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User preferences not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $preferences = $this->userPreferenceRepo->findForUser($user);

        if (!$preferences) {
            return ApiResponse::notFound('User preferences not found.');
        }

        return ApiResponse::success(new UserPreferencesResource($preferences), Response::HTTP_OK);
    }
}
