<?php

namespace App\Repositories\Contracts;

interface NewsRepositoryInterface
{
    public function fetchNews(array $data): array;
}
