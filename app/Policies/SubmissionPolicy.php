<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /** Mentor hanya boleh menilai pengiriman pada kelas miliknya sendiri. */
    public function review(User $user, Submission $submission): bool
    {
        $submission->loadMissing('assignment.lesson.module.course');

        return $user->hasRole('mentor')
            && $submission->assignment->lesson->module->course->mentor_id === $user->id;
    }

    /** Pengiriman boleh dilihat/diunduh oleh siswa pemiliknya atau mentor pemilik kelas. */
    public function view(User $user, Submission $submission): bool
    {
        return $submission->student_id === $user->id || $this->review($user, $submission);
    }
}
