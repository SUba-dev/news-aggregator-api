<?php

namespace App\Providers;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use app\Repositories\EloquentArticleRepository;
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
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
