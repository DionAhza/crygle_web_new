<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCoursesController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans): View
    {
        $user = $request->user();
        $this->syncPending($user, $midtrans);

        $enrollments = $user->enrollments()
            ->with('course.mentor.mentorType')
            ->latest()
            ->get();

        $active = $enrollments->where('payment_status', 'paid')->values();
        $courseIds = $active->pluck('course_id')->all();

        // Total materi per kelas (untuk teks "5/8 Modul" di kartu).
        $totals = Lesson::query()
            ->join('modules', 'modules.id', '=', 'lessons.module_id')
            ->whereIn('modules.course_id', $courseIds)
            ->selectRaw('modules.course_id as course_id, count(lessons.id) as total')
            ->groupBy('modules.course_id')
            ->pluck('total', 'course_id');

        // Materi yang sudah diselesaikan siswa per kelas.
        $done = LessonCompletion::query()
            ->join('lessons', 'lessons.id', '=', 'lesson_completions.lesson_id')
            ->join('modules', 'modules.id', '=', 'lessons.module_id')
            ->where('lesson_completions.student_id', $user->id)
            ->whereIn('modules.course_id', $courseIds)
            ->selectRaw('modules.course_id as course_id, count(lesson_completions.id) as done')
            ->groupBy('modules.course_id')
            ->pluck('done', 'course_id');

        return view('student.courses', [
            'active' => $active,
            'unpaid' => $enrollments->where('payment_status', '!=', 'paid'),
            'lessonTotals' => $totals,
            'lessonDone' => $done,
        ]);
    }

    /** Aktifkan otomatis kelas yang sudah dibayar tetapi webhook-nya belum masuk. */
    private function syncPending($user, MidtransService $midtrans): void
    {
        if (! $midtrans->configured()) {
            return;
        }

        $user->enrollments()
            ->where('payment_status', 'pending')
            ->whereNotNull('midtrans_order_id')
            ->with('course')
            ->get()
            ->each(fn ($e) => $midtrans->syncEnrollment($e));
    }
}
