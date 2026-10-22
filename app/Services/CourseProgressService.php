<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CourseProgressService
{
    /**
     * Seluruh lesson dalam kelas, berurutan: sort_order modul, lalu sort_order lesson.
     *
     * @return Collection<int, Lesson>
     */
    public function orderedLessons(Course $course): Collection
    {
        return Lesson::query()
            ->select('lessons.*')
            ->join('modules', 'modules.id', '=', 'lessons.module_id')
            ->where('modules.course_id', $course->id)
            ->orderBy('modules.sort_order')
            ->orderBy('modules.id')
            ->orderBy('lessons.sort_order')
            ->orderBy('lessons.id')
            ->with(['assignment', 'module:id,course_id,title,sort_order'])
            ->get();
    }

    /** @return array<int, int> daftar lesson_id yang sudah diselesaikan siswa di kelas ini */
    public function completedLessonIds(User $student, Course $course): array
    {
        return LessonCompletion::query()
            ->where('student_id', $student->id)
            ->whereIn('lesson_id', fn ($q) => $q->select('lessons.id')
                ->from('lessons')
                ->join('modules', 'modules.id', '=', 'lessons.module_id')
                ->where('modules.course_id', $course->id))
            ->pluck('lesson_id')
            ->all();
    }

    /**
     * Status tiap lesson: 'completed' | 'unlocked' | 'locked'.
     * Lesson terbuka hanya jika lesson sebelumnya (berdasarkan urutan) sudah selesai.
     *
     * @param  Collection<int, Lesson>  $lessons
     * @param  array<int, int>  $completedIds
     * @return array<int, string> [lesson_id => status]
     */
    public function lessonStates(Collection $lessons, array $completedIds): array
    {
        $done = array_flip($completedIds);
        $states = [];
        $previousDone = true;

        foreach ($lessons as $lesson) {
            if (isset($done[$lesson->id])) {
                $states[$lesson->id] = 'completed';
                $previousDone = true;
            } else {
                $states[$lesson->id] = $previousDone ? 'unlocked' : 'locked';
                $previousDone = false;
            }
        }

        return $states;
    }

    /** Lesson yang harus dikerjakan berikutnya (pertama yang belum selesai), atau null jika semua selesai. */
    public function nextLesson(Collection $lessons, array $states): ?Lesson
    {
        return $lessons->first(fn (Lesson $l) => $states[$l->id] === 'unlocked');
    }

    /**
     * Menandai lesson selesai (idempotent) lalu menghitung ulang progres.
     * Pemanggil bertanggung jawab memastikan lesson terbuka dan syarat tugas terpenuhi.
     */
    public function markCompleted(User $student, Lesson $lesson, Enrollment $enrollment): void
    {
        LessonCompletion::firstOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()],
        );

        $this->recalculate($enrollment);
    }

    /**
     * progress_percent = lesson selesai / total lesson * 100 (100 hanya jika semua selesai).
     * Saat mencapai 100: isi completed_at dan buat certificate_code unik.
     */
    public function recalculate(Enrollment $enrollment): Enrollment
    {
        $course = $enrollment->course ?? Course::findOrFail($enrollment->course_id);
        $total = $course->lessons()->count();
        $done = $total === 0 ? 0 : count($this->completedLessonIds($enrollment->student, $course));

        $percent = $total === 0 ? 0 : intdiv(min($done, $total) * 100, $total);

        $attributes = ['progress_percent' => $percent];

        if ($percent === 100 && $enrollment->completed_at === null) {
            $attributes['completed_at'] = now();
            $attributes['certificate_code'] ??= $this->generateCertificateCode();
        }

        $enrollment->update($attributes);

        return $enrollment;
    }

    private function generateCertificateCode(): string
    {
        do {
            $code = config('crygle.certificate_prefix').strtoupper(Str::random(10));
        } while (Enrollment::where('certificate_code', $code)->exists());

        return $code;
    }
}
