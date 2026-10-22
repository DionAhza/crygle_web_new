<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubmissionRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\CourseProgressService;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;

class SubmissionController extends Controller
{
    public function __construct(
        private CourseProgressService $progress,
        private SubmissionService $submissions,
    ) {}

    public function store(SubmissionRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        abort_unless($lesson->module()->where('course_id', $course->id)->exists(), 404);

        $assignment = $lesson->assignment;
        abort_unless($assignment, 404, 'Materi ini tidak memiliki tugas.');

        $student = $request->user();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->first();
        abort_unless($enrollment, 403);

        // Tugas hanya bisa dikirim jika materinya sudah terbuka (belum terkunci).
        $lessons = $this->progress->orderedLessons($course);
        $states = $this->progress->lessonStates($lessons, $this->progress->completedLessonIds($student, $course));

        if (($states[$lesson->id] ?? 'locked') === 'locked') {
            return back()->withErrors(['submission' => 'Materi ini masih terkunci. Selesaikan materi sebelumnya terlebih dahulu.']);
        }

        $this->submissions->submit(
            $assignment,
            $student,
            $request->safe()->only(['attachment_url', 'student_note']),
            $request->file('attachment'),
        );

        return redirect()->route('learn.show', [$course, $lesson])
            ->with('status', 'Tugas berhasil dikirim. Mentor akan segera meninjaunya.');
    }
}
