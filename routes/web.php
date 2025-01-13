<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/test', [NewsController::class, 'fetchNewsApi']);
Route::get('/g', [NewsController::class, 'fetchGuardianNews']);

Route::get('/', [NewsController::class, 'index']);
Route::get('/articles/list', [ArticleController::class, 'list']);
Route::get('/articles/show', [ArticleController::class, 'show']);
Route::get('/articles/search', [ArticleController::class, 'search']);
