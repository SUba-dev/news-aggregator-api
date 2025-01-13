<?php

namespace App\Factories;

use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Repositories\NewsApiOrgRepository;
use App\Repositories\GuardianApiRepository;

class NewsRepositoryFactory
{
    public static function make(string $source): NewsRepositoryInterface
    {
        return match ($source) {
            'newsapi' => new NewsApiOrgRepository(),            
            'guardian' => new GuardianApiRepository (),            
            default => throw new \InvalidArgumentException("Invalid news source: $source"),
        };
    }
}
