<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (!$this->resource) { 
            return []; 
        }
        return [
            'id' =>$this->id,
            'source' =>$this->source,
            'title' =>$this->title,
            'author' =>$this->author,
            'description' =>$this->description,
            'content' =>$this->content,
            'url' =>$this->url,
            'publishedAt' =>$this->published_at,
        ];
    }
}
