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
        
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
