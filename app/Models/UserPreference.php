<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'preferred_sources', 'preferred_categories', 'preferred_authors'];

    protected $casts = [
        'preferred_sources' => 'array',
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function sources() 
    {
        return $this->belongsToMany(NewsSource::class, 'user_preferred_sources'); 
    }

    public function categories() 
    {
        return $this->belongsToMany(Category::class, 'user_preferred_categories'); 
    }

    public function setSourcesAttribute($value)
    {
        $this->attributes['preferred_sources'] = json_encode($value);
    }

    public function getSourcesAttribute($value)
    {
        return json_decode($value, true); 
    }

    public function setCategoriesAttribute($value)
    {
        $this->attributes['preferred_categories'] = json_encode($value);
    }

    public function getCategoriesAttribute($value)
    {
        return json_decode($value, true); 
    }

    public function setAuthorsAttribute($value)
    {
        $this->attributes['preferred_authors'] = json_encode($value);
    }

    public function getAuthorsAttribute($value)
    {
        return json_decode($value, true); 
    }
}
