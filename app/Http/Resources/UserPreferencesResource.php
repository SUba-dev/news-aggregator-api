<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferencesResource extends JsonResource
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
            'userId' =>$this->user_id,
            'preferred_sources' =>$this->preferred_sources,
            'preferred_categories' =>$this->preferred_categories,
            'preferred_authors' =>$this->preferred_authors,
        ];
    }
}
