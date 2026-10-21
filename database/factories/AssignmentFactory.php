<?php

namespace Database\Factories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => 'Tugas: '.fake()->sentence(3),
            'instructions' => fake()->paragraph(),
            'submission_type' => 'repo_link',
            'passing_score' => (int) config('crygle.default_passing_score', 75),
        ];
    }
}
