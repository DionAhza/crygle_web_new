<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'module_id' => Module::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(4)),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_seconds' => fake()->numberBetween(180, 1200),
            'content_text' => fake()->paragraphs(3, true),
            'sort_order' => 1,
            'is_free_preview' => false,
        ];
    }
}
