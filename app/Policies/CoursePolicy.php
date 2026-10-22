<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /** Mentor hanya boleh mengelola kelas miliknya sendiri. */
    public function manage(User $user, Course $course): bool
    {
        return $user->hasRole('mentor') && $course->mentor_id === $user->id;
    }
}
