<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    public function __construct(private CourseProgressService $progress) {}

    public function disk(): string
    {
        return config('crygle.submission_disk', 'local');
    }

    /** Pengiriman terbaru siswa untuk tugas ini (atau null jika belum pernah mengirim). */
    public function latestFor(Assignment $assignment, User $student): ?Submission
    {
        return Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->latest('id')
            ->first();
    }

    /**
     * Seluruh riwayat pengiriman siswa untuk tugas ini, terbaru lebih dulu.
     *
     * @return Collection<int, Submission>
     */
    public function historyFor(Assignment $assignment, User $student): Collection
    {
        return Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->with('reviewer:id,name')
            ->latest('id')
            ->get();
    }

    /** Siswa boleh mengirim jika belum pernah mengirim atau pengiriman terakhirnya ditolak. */
    public function canSubmit(?Submission $latest): bool
    {
        return $latest === null || $latest->isRejected();
    }

    /**
     * Menyimpan pengiriman baru. Selalu INSERT (tidak menimpa) agar riwayat revisi tetap ada.
     *
     * @param  array{attachment_url?: ?string, student_note?: ?string}  $data
     *
     * @throws ValidationException
     */
    public function submit(Assignment $assignment, User $student, array $data, ?UploadedFile $file = null): Submission
    {
        return DB::transaction(function () use ($assignment, $student, $data, $file) {
            // Kunci baris tugas agar dua pengiriman bersamaan tidak lolos bersama-sama.
            Assignment::whereKey($assignment->id)->lockForUpdate()->first();

            $latest = $this->latestFor($assignment, $student);

            if (! $this->canSubmit($latest)) {
                throw ValidationException::withMessages([
                    'submission' => $latest->isApproved()
                        ? 'Tugas ini sudah disetujui mentor.'
                        : 'Pengiriman sebelumnya masih menunggu review mentor.',
                ]);
            }

            $attachment = match ($assignment->submission_type) {
                'file' => $file?->store("submissions/{$assignment->id}/{$student->id}", $this->disk()),
                'repo_link' => $data['attachment_url'] ?? null,
                default => null,
            };

            return Submission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'attachment_url' => $attachment,
                'student_note' => $data['student_note'] ?? null,
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Mentor menilai pengiriman: 'approved' atau 'rejected'.
     * Saat disetujui, materi dihitung selesai sehingga progres, materi berikutnya,
     * dan sertifikat otomatis ikut diperbarui.
     *
     * @throws ValidationException
     */
    public function review(Submission $submission, User $reviewer, string $decision, int $score, ?string $feedback): Submission
    {
        return DB::transaction(function () use ($submission, $reviewer, $decision, $score, $feedback) {
            $locked = Submission::whereKey($submission->id)
                ->lockForUpdate()
                ->with('assignment.lesson.module', 'student')
                ->firstOrFail();

            if (! $locked->isPending()) {
                throw ValidationException::withMessages([
                    'decision' => 'Pengiriman ini sudah pernah direview.',
                ]);
            }

            $locked->update([
                'status' => $decision,
                'score' => $score,
                'mentor_feedback' => $feedback,
                'reviewer_id' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            if ($decision === 'approved') {
                $lesson = $locked->assignment->lesson;

                $enrollment = Enrollment::where('student_id', $locked->student_id)
                    ->where('course_id', $lesson->module->course_id)
                    ->where('payment_status', 'paid')
                    ->first();

                if ($enrollment) {
                    $this->progress->markCompleted($locked->student, $lesson, $enrollment);
                }
            }

            return $locked;
        });
    }
}
