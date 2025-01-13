<?php

namespace App\Services;

use App\Models\Category;
use App\Models\NewsSource;
use Illuminate\Support\Facades\Cache;

class CacheService
{

    /**
     * Cache key for sources.
     */
    const SOURCES_CACHE_KEY = 'sources';

    /**
     * Cache key for categories.
     */
    const CATEGORIES_CACHE_KEY = 'categories';

    /**
     * Gets the source ID by its name.
     *
     * @param string $sourceName The name of the source.
     * @return int|null The ID of the source or null if not found.
     */
    public function getSourceIdByName($sourceName)
    {
        $sources = $this->sourceCache();
        if ($sources) {
            $source = $sources->firstWhere('name', $sourceName);
            return $source ? $source->id : null;
        }
        return null;
    }

    /**
     * Gets the category ID by its name.
     *
     * @param string $categoryName The name of the category.
     * @return int|null The ID of the category or null if not found.
     */
    public function getCategoryIdByName($categoryName)
    {
        $categories = $this->categoryCache();
        if ($categories) {
            $category = $categories->firstWhere('name', $categoryName);
            return $category ? $category->id : null;
        }
        return null;
    }

    /**
     * Gets a value from the cache.
     *
     * @param string $key The cache key.
     * @return mixed The cached value or null if not found.
     */
    public function get($key)
    {
        return Cache::get($key);
    }


    /**
     * Stores a value in the cache.
     *
     * @param string $key The cache key.
     * @param mixed $value The value to store.
     * @param int $hours The number of hours to cache the value.
     * @return bool True if the value was successfully stored, false otherwise.
     */
    public function put($key, $value, $hours = 3)
    {
        return Cache::put($key, $value, now()->addHour($hours));
    }

    /**
     * Checks if a key exists in the cache.
     *
     * @param string $key The cache key.
     * @return bool True if the key exists, false otherwise.
     */
    public function has($key)
    {
        return Cache::has($key);
    }

    /**
     * Removes a value from the cache.
     *
     * @param string $key The cache key.
     * @return bool True if the value was successfully removed, false otherwise.
     */
    public function forget($key)
    {
        return Cache::forget($key);
    }

    /**
     * Retrieves all categories from the cache.
     *
     * @return Collection|null The collection of categories or null if not found.
     */
    public function categoryCache()
    {
        $categories = $this->get(self::CATEGORIES_CACHE_KEY);
        if (!$categories) {
            $categories = Category::all();
            $this->put(self::CATEGORIES_CACHE_KEY, $categories, 10);
        }
        return $categories;
    }

    /**
     * Retrieves all sources from the cache.
     *
     * @return Collection|null The collection of sources or null if not found.
     */
    public function sourceCache()
    {
        $sources = $this->get(self::SOURCES_CACHE_KEY);
        if (!$sources) {
            $sources = NewsSource::all();
            $this->put(self::SOURCES_CACHE_KEY, $sources, 10);
        }
        return $sources;
    }
}
