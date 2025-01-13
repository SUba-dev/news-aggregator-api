<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\DTOs\NewsArticleDto;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Services\CacheService;
use App\Services\NewsFetchService;
use Mockery;

class NewsFetchServiceTest extends TestCase
{

    /**
     * 
     * Test news service store articles
     * 
     */

    public function testNewsStoresArticles()
    {
        // Mock repository and cache service
        $mockRepo = Mockery::mock(NewsRepositoryInterface::class);
        $mockCacheService = Mockery::mock(CacheService::class);

        // Mock repository response
        $mockRepo->shouldReceive('fetchNews')
            ->once()
            ->with([
                'q' => 'test',
                'from' => now()->subDay()->format('Y-m-d'),
                'to' => now()->format('Y-m-d'),
                'sortBy' => 'popularity',
                'pageSize' => 20,
            ])
            ->andReturn([
                [
                    'source' => 'BBC News',
                    'author' => 'Suba',
                    'title' => 'Test Title',
                    'description' => 'Test Description',
                    'content' => 'Test Content',
                    'url' => 'https://example.com',
                    'publishedAt' => now()->toIso8601String(),
                ],
            ]);

        // Mock cache service behavior
        $mockCacheService->shouldReceive('getCategoryIdByName')
            ->with('general')
            ->andReturn(1);
        $mockCacheService->shouldReceive('getSourceIdByName')
            ->with('newsapi')
            ->andReturn(1);

        // Bind mocks to the service container
        $this->app->instance(NewsRepositoryInterface::class, $mockRepo);
        $this->app->instance(CacheService::class, $mockCacheService);

        // Resolving the service with mocked dependencies
        $service = app(NewsFetchService::class);

        // Calling the method being tested
        $result = $service->getNews('newsapi', [
            'q' => 'test',
            'from' => now()->subDay()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'sortBy' => 'popularity',
            'pageSize' => 20,
        ]);


        $this->assertNotEmpty($result);
        $this->assertEquals('Suba', $result[0]['author']);
        $this->assertEquals('BBC News', $result[0]['source']);
        $this->assertEquals('Test Title', $result[0]['title']);
    }


    /**
     * 
     * Test news service handle the empty response
     * 
     */

    public function testNewsHandlesEmptyResponse()
    {
        $mockRepo = Mockery::mock(NewsRepositoryInterface::class);
        $mockCacheService = Mockery::mock(CacheService::class);

        $mockRepo->shouldReceive('fetchNews')
            ->once()
            ->andReturn([]);

        $mockCacheService->shouldReceive('getCategoryIdByName')->andReturn(1);
        $mockCacheService->shouldReceive('getSourceIdByName')->andReturn(1);

        $this->app->instance(NewsRepositoryInterface::class, $mockRepo);
        $this->app->instance(CacheService::class, $mockCacheService);

        $service = app(NewsFetchService::class);

        $result = $service->getNews('newsapi', [
            'q' => 'test',
            'from' => now()->subDay()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'sortBy' => 'popularity',
            'pageSize' => 20,
        ]);

        $this->assertEmpty($result);
    }




    /**
     * 
     * Test news service handle the api failure
     * 
     */

    public function testNewsHandleApiFailure()
    {
        $mockRepo = Mockery::mock(NewsRepositoryInterface::class);
        $mockCacheService = Mockery::mock(CacheService::class);

        $mockRepo->shouldReceive('fetchNews')
            ->once()
            ->andThrow(new \Exception('Failed to fetch news'));

        $mockCacheService->shouldReceive('getCategoryIdByName')->andReturn(1);
        $mockCacheService->shouldReceive('getSourceIdByName')->andReturn(1);

        $this->app->instance(NewsRepositoryInterface::class, $mockRepo);
        $this->app->instance(CacheService::class, $mockCacheService);

        $service = app(NewsFetchService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to fetch news');

        $service->getNews('newsapi', [
            'q' => 'test',
            'from' => now()->subDay()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'sortBy' => 'popularity',
            'pageSize' => 20,
        ]);
    }


    
}
