<?php

namespace App\Services;

use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Str;

class CertificateService
{
    private const LEVELS = [
        'basic' => 'Dasar',
        'intermediate' => 'Menengah',
        'advanced' => 'Lanjutan',
    ];

    /** Sertifikat hanya sah untuk pendaftaran berbayar yang sudah selesai dan memiliki kode. */
    public function isIssued(Enrollment $enrollment): bool
    {
        return $enrollment->isPaid() && $enrollment->isCompleted() && filled($enrollment->certificate_code);
    }

    /** Cari pendaftaran berdasarkan kode sertifikat; null jika tidak ada atau belum sah. */
    public function findByCode(string $code): ?Enrollment
    {
        $enrollment = Enrollment::query()
            ->where('certificate_code', $code)
            ->with(['student:id,name', 'course.mentor:id,name'])
            ->first();

        return $enrollment && $this->isIssued($enrollment) ? $enrollment : null;
    }

    public function verifyUrl(Enrollment $enrollment): string
    {
        return route('certificates.verify', $enrollment->certificate_code);
    }

    /** Data yang dipakai bersama oleh PDF dan halaman verifikasi. */
    public function data(Enrollment $enrollment): array
    {
        $enrollment->loadMissing(['student:id,name', 'course.mentor:id,name']);
        $course = $enrollment->course;

        return [
            'studentName' => $enrollment->student->name,
            'courseTitle' => $course->title,
            'courseLevel' => self::LEVELS[$course->level] ?? ucfirst((string) $course->level),
            'mentorName' => $course->mentor->name,
            'lessonCount' => $course->lessons()->count(),
            'completedOn' => $enrollment->completed_at
                ->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y'),
            'code' => $enrollment->certificate_code,
            'verifyUrl' => $this->verifyUrl($enrollment),
        ];
    }

    public function render(Enrollment $enrollment): DomPdf
    {
        return Pdf::loadView('certificates.pdf', $this->data($enrollment))
            ->setPaper('a4', 'landscape');
    }

    public function filename(Enrollment $enrollment): string
    {
        $enrollment->loadMissing('course');

        return 'sertifikat-'.Str::slug($enrollment->course->title).'-'.Str::lower($enrollment->certificate_code).'.pdf';
    }
}
