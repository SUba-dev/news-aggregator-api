<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\DTOs\NewsArticleDto;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Services\CacheService;
use App\Services\NewsFetchService;
use Mockery;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class NewsFetchServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    
        // Insert or update news source record
        DB::table('news_sources')->updateOrInsert(
            ['id' => 1], 
            [
                'name' => 'newsapi',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    
        // Insert or update category record
        DB::table('categories')->updateOrInsert(
            ['id' => 1], 
            [
                'name' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * @test
     * Test news service store articles
     * 
     */

    public function test_news_stores_articles()
    {
        // Mock repository and cache service
        $mockRepo = Mockery::mock(NewsRepositoryInterface::class);
        $mockCacheService = Mockery::mock(CacheService::class);

        $faker = Faker::create();
        $title=$faker->sentence(6, true); 
        $author = $faker->name; 
        $source= $faker->company;
        
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
                    'source' => $source,
                    'author' => $author,
                    'title' => $title,
                    'description' => $faker->paragraph,
                    'content' => $faker->text,
                    'url' => $faker->url,
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
        $this->assertEquals($author, $result[0]['author']);
        $this->assertEquals($source, $result[0]['source']);
        $this->assertEquals($title, $result[0]['title']);
    }


    /**
     * @test
     * Test news service handle the empty response
     * 
     */

    public function test_news_handles_empty_response()
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
     * @test
     * Test news service handle the api failure
     * 
     */

    public function test_news_handle_api_failure()
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
