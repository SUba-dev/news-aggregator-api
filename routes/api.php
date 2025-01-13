<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['api.security', 'throttle:60,1'])
    ->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::prefix('auth')->group(function () {
            Route::post('register', [AuthController::class, 'register']);
            Route::post('login', [AuthController::class, 'login']);
            Route::post('forgot-password', [AuthController::class, 'forgetPassword']);
            Route::post('reset-password', [AuthController::class, 'resetPassword']);
        });
    });


Route::middleware(['auth:sanctum', 'api.security', 'throttle:60,1'])
    ->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        
        Route::prefix('articles')->group(function () {
            Route::get('list', [ArticleController::class, 'list']);
            Route::get('show', [ArticleController::class, 'show']);
            Route::get('search', [ArticleController::class, 'search']);
            Route::get('user-preferences', [ArticleController::class, 'personalizedArticle']);
        });

        Route::prefix('user')->group(function () {
            Route::post('preferences', [UserPreferenceController::class, 'store']);
            Route::get('preferences', [UserPreferenceController::class, 'show']);
        });
    });
