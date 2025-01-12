<?php

namespace App\Providers;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Contracts\UserPreferenceInterface;
use app\Repositories\EloquentArticleRepository;
use app\Repositories\EloquentUserPreferenceRepository;
use App\Services\CacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('CacheService', function ($app) {
            return new CacheService();
        });

        $this->app->bind(ArticleRepositoryInterface::class, EloquentArticleRepository::class);        
        // $this->app->bind(UserPreferenceInterface::class, EloquentUserPreferenceRepository::class);  
        $this->app->bind(UserPreferenceInterface::class, function ($app) {
            return new EloquentUserPreferenceRepository(); 
        });      
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
