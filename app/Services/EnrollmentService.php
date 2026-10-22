<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentService
{
    /**
     * Mendaftarkan siswa ke kelas. Kelas gratis langsung berstatus paid;
     * kelas berbayar berstatus pending sampai pembayaran dikonfirmasi.
     * Idempotent: pendaftaran yang sudah paid tidak diubah.
     */
    public function enroll(User $student, Course $course): Enrollment
    {
        $enrollment = Enrollment::firstOrCreate(
            ['student_id' => $student->id, 'course_id' => $course->id],
            ['payment_status' => $course->isFree() ? 'paid' : 'pending', 'progress_percent' => 0],
        );

        if (! $enrollment->isPaid()) {
            $enrollment->update(['payment_status' => $course->isFree() ? 'paid' : 'pending']);
        }

        return $enrollment;
    }
}
