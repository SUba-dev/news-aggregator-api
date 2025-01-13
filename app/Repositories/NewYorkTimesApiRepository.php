<?php

namespace App\Repositories;

use app\DTOs\NewsArticleDto;
use App\Repositories\Contracts\NewsRepositoryInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Exception;
use Illuminate\Support\Str;

class NewYorkTimesApiRepository implements NewsRepositoryInterface
{
    private string $apiUrl;
    private string $apiKey;

    public function fetchNews(array $data): array
    {
         
        $this->apiKey = Config::get('api-config.newyorktimes.api-key') ?? "";
        $this->apiUrl = Config::get('api-config.newyorktimes.api-url') ?? "";

        $defaultQuery = [
            'api-key' => $this->apiKey,
        ];

        $queryParams = array_merge($defaultQuery, $data);

        $url = $this->apiUrl . '?' . http_build_query($queryParams);
        try {
            $client = new Client();
            $response = $client->get($url);

            if ($response->getStatusCode() === 200) {
                $news = json_decode($response->getBody(), true);
                
                $newsArr = $news['response']['docs'];
                
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
        if (isset($news) && !empty($news)) {
            foreach ($news as $key => $article) {                 
                $dto = array(
                    'source' => $this->cleanCode($article['source']) ?? 'New York Times',
                    'author' => "",
                    'title' => $this->cleanCode(Str::limit($article['snippet'], 140, '...')) ?? 'No Title',                   
                    'description' => $this->cleanCode(Str::limit($article['lead_paragraph']), 200, '...') ?? null,
                    'content' => "",
                    'url' => $this->cleanCode($article['web_url']) ?? 'No URL',
                    'publishedAt' => $this->cleanCode($article['pub_date']) ?? null
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
