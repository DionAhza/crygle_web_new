<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;
use App\Services\CertificateService;

class EnrollmentPolicy
{
    /** Hanya siswa pemilik, pada pendaftaran berbayar yang sudah selesai 100% dan bersertifikat. */
    public function downloadCertificate(User $user, Enrollment $enrollment): bool
    {
        return $enrollment->student_id === $user->id
            && app(CertificateService::class)->isIssued($enrollment);
    }
}
