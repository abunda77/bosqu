<?php

namespace Database\Factories;

use App\Models\Post;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        $title = $this->faker->sentence;
        return [
            'title' => $title,
            'body' => $this->faker->paragraphs(3, true),
            'feature_image' => $this->faker->imageUrl(),
            'slug' => Str::slug($title),

            'status' => $this->faker->randomElement(['draft', 'published', 'private']),
        ];
    }
}
