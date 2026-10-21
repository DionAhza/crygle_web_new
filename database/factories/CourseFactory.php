<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'mentor_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'description' => fake()->paragraph(),
            'price' => fake()->randomElement([0, 99000, 199000, 349000]),
            'level' => fake()->randomElement(['basic', 'intermediate', 'advanced']),
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
