<?php

namespace App\DTOs;

use Ramsey\Uuid\Type\Integer;

class NewsArticleDto 
{
    public ?string $title;
    public ?string $author;
    public ?string $description;
    public ?string $content;
    public ?string $url;
    public ?string $publishedAt;
    public string $source;
    public int $sourceId;
    public int $categoryId;

    public function __construct(
        string $source,
        int $sourceId,
        int $categoryId,
        ?string $author = null,
        string $title = 'No Title',
        ?string $description = null,
        ?string $content = null,
        string $url = 'No URL',
        ?string $publishedAt = null
    ) {
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->categoryId = $categoryId;
        $this->author = $author;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->url = $url;
        $this->publishedAt = $publishedAt;
    }
}
