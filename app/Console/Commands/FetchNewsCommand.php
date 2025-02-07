<?php

namespace App\Console\Commands;

use App\Jobs\GuradianNewsJob;
use App\Jobs\NewsApiNewsJob;
use App\Jobs\NewYorkTimesJob;
use App\Services\NewsFetchService;
use Illuminate\Console\Command;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from sources and store in the database';


    /**
     * Execute the console command.
     */
    public function handle()
    {        
        try {
            dispatch(new NewsApiNewsJob())->onQueue('default');

            dispatch(new GuradianNewsJob())->onQueue('default');

            dispatch(new NewYorkTimesJob())->onQueue('default');

            $this->info('News fetched successfully.');
            
        } catch (\Exception $e) {
            $this->error('Error fetching news: ' . $e->getMessage());
        }
        
    }
}
