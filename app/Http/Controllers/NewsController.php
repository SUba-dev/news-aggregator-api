<?php

namespace App\Http\Controllers;

use App\Services\NewsFetchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    private NewsFetchService $newsFetchService;

    public function __construct(NewsFetchService $newsFetchService)
    {
        $this->newsFetchService = $newsFetchService;
    }

    public function fetchNewyorkTimes(): JsonResponse
    {
        $source = 'newyork_times'; //
        try {
            $randomQuery = Arr::random(config('constants.default_query', ['technology']));
            $data = [
                'q' => $randomQuery,
                // 'sort' => "relevance",
                // 'begin_date' => now()->subDay()->format('Y-m-d'),
                // 'end_date' => now()->format('Y-m-d'),
                'page' => 1,

            ];
            $news = $this->newsFetchService->getNews($source, $data);

            return response()->json(['status' => true, 'data' => $news]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }



    public function fetchGuardianNews(): JsonResponse
    {
        $source = 'guardian'; //
        try {
            $randomQuery = Arr::random(config('constants.default_query', ['technology']));
            $data = [
                'q' => $randomQuery,
                // 'tag' => "politics",
                // 'from-date' => now()->subDay()->format('Y-m-d'),
                'page' => 1,

            ];
            $news = $this->newsFetchService->getNews($source, $data);

            return response()->json(['status' => true, 'data' => $news]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }


    public function fetchNewsApi(): JsonResponse
    {
        $source = 'newsapi';
        try {
            $randomQuery = Arr::random(config('constants.default_query', ['technology']));
            $data = [
                'q' => $randomQuery,
                'from' => now()->subDay()->format('Y-m-d'),
                'to' => now()->format('Y-m-d'),
                'sortBy' => config('news.default_sort', 'popularity'),
                'pageSize' => 20,
            ];
            $news = $this->newsFetchService->getNews($source, $data);

            return response()->json(['status' => true, 'data' => $news]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
