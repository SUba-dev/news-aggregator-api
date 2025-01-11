<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\NewsSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artical>
 */
class ArticalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source' => $this->faker->company,
            'news_source_id' => NewsSource::inRandomOrder()->first()->id, 
            'category_id' => Category::inRandomOrder()->first()->id, 
            'title' => $this->faker->sentence(6, true), 
            'author' => $this->faker->name, 
            'description' => $this->faker->paragraph, 
            'content' => $this->faker->text, 
            'url' => $this->faker->url, 
            'published_at' => $this->faker->dateTimeThisYear, 
        ];
    }
}
