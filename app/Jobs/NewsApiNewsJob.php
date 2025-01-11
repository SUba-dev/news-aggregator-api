<?php

namespace App\Jobs;

use App\Services\NewsFetchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class NewsApiNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying a job that failed.
     *
     * @var int
     */
    public int $backoff = 10;


    /**
     * Create a new job instance.
     */
    private string $source;
    private array $query;

    public function __construct()
    {
        $this->source = 'newsapi';
        $this->query = [
            'q' => Arr::random(config('constants.default_query', ['technology'])),
            'from' => now()->subDay()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'sortBy' => 'popularity',
            'pageSize' => 20,
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(NewsFetchService $newsFetchService): void
    {
        $newsFetchService->getNews($this->source, $this->query);
    }
}
