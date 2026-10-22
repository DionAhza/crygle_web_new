<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'course_id' => Course::factory(),
            'payment_status' => 'paid',
            'progress_percent' => 0,
        ];
    }

    /** Kelas sudah selesai 100% dan sertifikat sudah terbit. */
    public function completed(): static
    {
        return $this->state(fn () => [
            'payment_status' => 'paid',
            'progress_percent' => 100,
            'completed_at' => now(),
            'certificate_code' => config('crygle.certificate_prefix').Str::upper(Str::random(10)),
        ]);
    }
}
