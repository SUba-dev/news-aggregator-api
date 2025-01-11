<?php

namespace App\Services;

use App\Models\Category;
use App\Models\NewsSource;
use Illuminate\Support\Facades\Cache;

class CacheService
{

    public function getSourceIdByName($sourceName)
    {
        $sources = $this->sourceCache('sources');
        if ($sources) {
            $source = $sources->firstWhere('name', $sourceName);
            return $source ? $source->id : null;
        }
        return null;
    }


    public function getCategoryIdByName($categoryName)
    {
        $categories = $this->categoryCache('categories');
        if ($categories) {
            $category = $categories->firstWhere('name', $categoryName);
            return $category ? $category->id : null;
        }
        return null;
    }


    public function get($key)
    {
        return Cache::get($key);
    }

    public function put($key, $value, $hours = 3)
    {
        return Cache::put($key, $value, now()->addHour($hours));
    }

    public function has($key)
    {
        return Cache::has($key);
    }

    public function forget($key)
    {
        return Cache::forget($key);
    }

    public function categoryCache($categoryCacheKey)
    {
        $categories = $this->get($categoryCacheKey);
        if (!$categories) {
            $categories = Category::all();
            $this->put($categoryCacheKey, $categories, 10);
        }
        return $categories;
    }


    public function sourceCache($sourceCacheKey)
    {
        $sources = $this->get($sourceCacheKey);
        if (!$sources) {
            $sources = NewsSource::all();
            $this->put($sourceCacheKey, $sources, 10);
        }
        return $sources;
    }
}
