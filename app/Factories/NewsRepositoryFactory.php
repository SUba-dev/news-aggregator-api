<?php

namespace App\Factories;

use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Repositories\NewsApiOrgRepository;
use App\Repositories\GuardianApiRepository;
use App\Repositories\NewYorkTimesApiRepository;

class NewsRepositoryFactory
{
    public static function make(string $source): NewsRepositoryInterface
    {
        if (app()->environment('testing')) {
            return app(NewsRepositoryInterface::class);
        }
        
        return match ($source) {
            'newsapi' => new NewsApiOrgRepository(),            
            'guardian' => new GuardianApiRepository (), 
            'newyork_times' => new NewYorkTimesApiRepository (), 
                       
            default => throw new \InvalidArgumentException("Invalid news source: $source"),
        };
    }
}
