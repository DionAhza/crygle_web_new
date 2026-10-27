<?php

namespace Database\Factories;

use App\Models\MentorType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<MentorType> */
class MentorTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return ['name' => Str::title($name), 'slug' => Str::slug($name).'-'.Str::lower(Str::random(4))];
    }
}
