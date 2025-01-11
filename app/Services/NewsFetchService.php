<?php

namespace App\Services;

use App\DTOs\NewsArticleDto;
use App\Factories\NewsRepositoryFactory;
use App\Models\Artical;
use App\Models\Category;
use App\Models\NewsSource;
use app\Repositories\EloquentArticleRepository;

class NewsFetchService
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getNews(string $sourceQuery, array $data): array
    {
        $categoryQuery = "general";

        $newsRepo = NewsRepositoryFactory::make($sourceQuery);
        $newsArr = $newsRepo->fetchNews($data);

        if (!empty($newsArr)) {    

            $categoryId = $this->cacheService->getCategoryIdByName($categoryQuery);
            $sourceId = $this->cacheService->getSourceIdByName($sourceQuery);

            foreach ($newsArr as $key => $article) {
                
                $publishedAt = \Carbon\Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s');

                $newDto = new NewsArticleDto(
                    $article['source'],
                    $sourceId ?? 1,
                    $categoryId ?? 1,
                    $article['author'],
                    $article['title'],
                    $article['description'],
                    $article['content'],
                    $article['url'],
                    $article['publishedAt']
                );

                Artical::updateOrCreate([
                    'source' => $newDto->source ?? "",
                    'news_source_id' => $newDto->sourceId ?? 1,
                    'category_id' => $newDto->categoryId ?? 1,
                    'title' => $newDto->title ?? "",
                    'published_at' => $publishedAt,
                ],[
                    'author' => $newDto->author ?? "",
                    'description' => $newDto->description ?? "",
                    'content' => $newDto->content ?? "",
                    'url' => $newDto->url ?? "",
                    
                ]);
            }
        }
        return $newsArr;
    }
}
