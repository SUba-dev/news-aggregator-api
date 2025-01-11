<?php

namespace App\Repositories;

use app\DTOs\NewsArticleDto;
use App\Repositories\Contracts\NewsRepositoryInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Exception;
use Illuminate\Support\Str;

class NewsApiOrgRepository implements NewsRepositoryInterface
{
    private string $apiUrl;
    private string $apiKey;

    public function fetchNews(array $data): array
    {
        $this->apiKey = Config::get('api-config.newsapi.api-key') ?? "";
        $this->apiUrl = Config::get('api-config.newsapi.api-url') ?? "";

        $defaultQuery = [
            'country' => 'us',
            'apiKey' => $this->apiKey,
        ];

        $queryParams = array_merge($defaultQuery, $data);

        // $url = $this->newsApiUrl . '/' . $this->apiVersion . '/top-headlines?country=us&apiKey=' . $this->apiKey;
        $url = $this->apiUrl . '/top-headlines?' . http_build_query($queryParams);
        try {
            $client = new Client();
            $response = $client->get($url);

            if ($response->getStatusCode() === 200) {
                $news = json_decode($response->getBody(), true);
                return $this->newsResponse($news);
            } else {
                return ['status' => false, 'error' => 'Failed to fetch news data. Status code: ' . $response->getStatusCode()];
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            return ['status' => false, 'error' => 'There is server connection issue.'];
        } catch (Exception $e) {
            return ['status' => false, 'error' => 'An error occurred while fetching news data: ' . $e->getMessage()];
        }
    }






    private function newsResponse($news): array
    {
        $newsArticles = [];
        if (isset($news['articles']) && !empty($news['articles'])) {
            foreach ($news['articles'] as $key => $article) {
                $dto = array(
                    'source' => $this->cleanCode($article['source']['name']) ?? 'News API Org',
                    'author' => $this->cleanCode($article['author']) ?? null,
                    'title' => $this->cleanCode(Str::limit($article['title'], 140, '...')) ?? 'No Title',
                    'description' => $this->cleanCode(Str::limit($article['description']), 200, '...') ?? null,
                    'content' => $this->cleanCode(Str::limit($article['content'], 300, '...')) ?? null,
                    'url' => $this->cleanCode($article['url']) ?? 'No URL',
                    'publishedAt' => $this->cleanCode($article['publishedAt']) ?? null
                );
                $newsArticles[] = $dto;
            }
        }
        return $newsArticles;
    }


    private function cleanCode($data)
    {
        if (empty($data)) {
            return null; 
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}
