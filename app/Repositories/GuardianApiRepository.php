<?php

namespace App\Repositories;

use app\DTOs\NewsArticleDto;
use App\Repositories\Contracts\NewsRepositoryInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Exception;
use Illuminate\Support\Str;

class GuardianApiRepository implements NewsRepositoryInterface
{
    private string $apiUrl;
    private string $apiKey;

    public function fetchNews(array $data): array
    {
         
        $this->apiKey = Config::get('api-config.guardian.api-key') ?? "";
        $this->apiUrl = Config::get('api-config.guardian.api-url') ?? "";

        $defaultQuery = [
            'api-key' => $this->apiKey,
        ];

        $queryParams = array_merge($defaultQuery, $data);

        $url = $this->apiUrl . '/search?' . http_build_query($queryParams);
        try {
            $client = new Client();
            $response = $client->get($url);

            if ($response->getStatusCode() === 200) {
                $news = json_decode($response->getBody(), true);
                $newsArr = $news['response'];
                return $this->newsResponse($newsArr);
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
        if (isset($news['results']) && !empty($news['results'])) {
            foreach ($news['results'] as $key => $article) {
                $dto = array(
                    'source' => $this->cleanCode($article['sectionName']) ?? 'News API Org',
                    'author' => "",
                    'title' => $this->cleanCode(Str::limit($article['webTitle'], 140, '...')) ?? 'No Title',
                    'description' => "",
                    'content' => "",
                    'url' => $this->cleanCode($article['webUrl']) ?? 'No URL',
                    'publishedAt' => $this->cleanCode($article['webPublicationDate']) ?? null
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
