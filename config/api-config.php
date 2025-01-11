<?php
/*
|--------------------------------------------------------------------------
| News API Configuration Environments 
|--------------------------------------------------------------------------
|
|
|
*/
return [
    'newsapi' => [
        'api-key' => env('NEWS_API_KEY', ''),
        'api-url' => 'https://newsapi.org/v2',
    ],
    'guardian' => [
        'api-key' => env('GUARDIAN_API_KEY', ''),
        'api-url' => 'https://newsapi.org/v2',
    ],
    'newyorktimes' => [
        'api-key' => env('NEWYORKTIMES_API_KEY', ''),
        'api-secret' => env('NEWYORKTIMES_API_SECRET', ''),
        'api-url' => 'https://newsapi.org/v2',
    ],
];