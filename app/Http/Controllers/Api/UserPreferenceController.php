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


    public function store(UserPreferenceRequest $request): JsonResponse
    {
        $user = $request->user();
        $this->userPreferenceRepo->store($user,  $request->all());

        return ApiResponse::success(new UserPreferencesResource($user->fresh()->preferences), Response::HTTP_OK);
    }

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
