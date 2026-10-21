<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'student_id' => User::factory(),
            'attachment_url' => 'https://github.com/example/'.fake()->slug(2),
            'student_note' => fake()->sentence(),
            'status' => 'pending',
        ];
    }

    public function approved(int $score = 85): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'score' => $score,
            'mentor_feedback' => 'Kerja bagus!',
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(int $score = 55): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'score' => $score,
            'mentor_feedback' => 'Silakan perbaiki dan kirim ulang.',
            'reviewed_at' => now(),
        ]);
    }
}
