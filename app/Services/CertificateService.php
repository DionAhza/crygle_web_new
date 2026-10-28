<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Submission;
use App\Support\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
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
            'qr' => $this->qrDataUri($this->verifyUrl($enrollment)),
            'assessments' => $assessments = $this->assessments($enrollment),
            'totalScore' => $assessments->isEmpty() ? null : (int) round($assessments->avg('score')),
        ];
    }

    /** QR ke halaman verifikasi (null bila ekstensi GD tidak tersedia). */
    private function qrDataUri(string $url): ?string
    {
        if (! function_exists('imagecreate')) {
            return null;
        }

        return QrCode::pngDataUri($url, 8, 1);
    }

    /**
     * Nilai tiap tugas kelas (skor terbaik dari pengumpulan yang disetujui) untuk halaman
     * "Assessment Results". Tugas yang belum bernilai dilewati.
     *
     * @return Collection<int, array{title: string, detail: string, score: int}>
     */
    public function assessments(Enrollment $enrollment): Collection
    {
        $lessons = app(CourseProgressService::class)->orderedLessons($enrollment->course);

        $scores = Submission::query()
            ->where('student_id', $enrollment->student_id)
            ->where('status', 'approved')
            ->whereIn('assignment_id', $lessons->pluck('assignment.id')->filter())
            ->get()
            ->groupBy('assignment_id')
            ->map(fn ($subs) => (int) $subs->max('score'));

        return $lessons
            ->filter(fn ($lesson) => $lesson->assignment && $scores->has($lesson->assignment->id))
            ->map(fn ($lesson) => [
                'title' => $lesson->assignment->title,
                'detail' => $this->plain($lesson->assignment->instructions) ?: $lesson->title,
                'score' => $scores[$lesson->assignment->id],
            ])
            ->values();
    }

    /** Teks polos singkat dari instruksi (markdown) agar muat di sel tabel. */
    private function plain(?string $markdown): string
    {
        $text = Str::of(Str::markdown((string) $markdown, ['html_input' => 'strip']))->stripTags()->squish()->toString();

        return Str::limit($text, 110);
    }

    public function render(Enrollment $enrollment): DomPdf
    {
        // dompdf menyimpan cache font (Plus Jakarta Sans) di storage/fonts.
        File::ensureDirectoryExists(storage_path('fonts'));

        return Pdf::loadView('certificates.pdf', $this->data($enrollment))
            ->setPaper('a4', 'landscape');
    }

    public function filename(Enrollment $enrollment): string
    {
        $enrollment->loadMissing('course');

        return 'sertifikat-'.Str::slug($enrollment->course->title).'-'.Str::lower($enrollment->certificate_code).'.pdf';
    }
}
