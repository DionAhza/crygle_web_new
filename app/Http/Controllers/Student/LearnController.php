<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\CourseProgressService;
use App\Services\SubmissionService;
use App\Support\VideoEmbed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LearnController extends Controller
{
    public function __construct(
        private CourseProgressService $progress,
        private SubmissionService $submissions,
    ) {}

    /** Mengarahkan ke materi yang harus dikerjakan berikutnya. */
    public function index(Request $request, Course $course): RedirectResponse
    {
        $user = $request->user();
        $enrollment = $this->paidEnrollment($request, $course);
        $lessons = $this->progress->orderedLessons($course);

        abort_if($lessons->isEmpty(), 404, 'Kelas ini belum memiliki materi.');

        if ($enrollment) {
            $states = $this->progress->lessonStates($lessons, $this->progress->completedLessonIds($user, $course));
            $target = $this->progress->nextLesson($lessons, $states) ?? $lessons->last();
        } elseif ($user->id === $course->mentor_id) {
            $target = $lessons->first();
        } else {
            return redirect()->route('catalog.show', $course)->withErrors(['course' => 'Anda belum terdaftar di kelas ini.']);
        }

        return redirect()->route('learn.show', [$course, $target]);
    }

    public function show(Request $request, Course $course, Lesson $lesson): View|RedirectResponse
    {
        $user = $request->user();
        $this->ensureLessonInCourse($course, $lesson);

        $enrollment = $this->paidEnrollment($request, $course);
        $isOwner = $user->id === $course->mentor_id;

        $lessons = $this->progress->orderedLessons($course);
        $completedIds = $enrollment ? $this->progress->completedLessonIds($user, $course) : [];
        $states = $enrollment
            ? $this->progress->lessonStates($lessons, $completedIds)
            : $lessons->mapWithKeys(fn ($l) => [$l->id => $isOwner ? 'unlocked' : 'locked'])->all();

        if ($enrollment) {
            $mode = 'student';
            if ($states[$lesson->id] === 'locked') {
                $next = $this->progress->nextLesson($lessons, $states);

                return redirect()->route('learn.show', [$course, $next ?? $lessons->first()])
                    ->withErrors(['lesson' => 'Selesaikan materi sebelumnya terlebih dahulu untuk membuka materi ini.']);
            }
        } elseif ($isOwner) {
            $mode = 'owner';
        } elseif ($course->is_published && $lesson->is_free_preview) {
            $mode = 'preview';
        } else {
            return redirect()->route('catalog.show', $course)->withErrors(['course' => 'Daftar kelas ini untuk mengakses materi.']);
        }

        $index = $lessons->search(fn ($l) => $l->id === $lesson->id);
        $prev = $index > 0 ? $lessons[$index - 1] : null;
        $next = $lessons[$index + 1] ?? null;
        $isCompleted = ($states[$lesson->id] ?? null) === 'completed';

        $lesson->loadMissing('assignment');
        $course->loadMissing('mentor.mentorType');

        // Riwayat pengiriman tugas (terbaru dulu) hanya relevan untuk siswa terdaftar.
        $submissions = collect();
        if ($mode === 'student' && $lesson->assignment) {
            $submissions = $this->submissions->historyFor($lesson->assignment, $user);
        }

        return view('student.learn', [
            'course' => $course,
            'lesson' => $lesson,
            'lessons' => $lessons->groupBy('module_id'),
            'modules' => $lessons->pluck('module')->unique('id')->values(),
            'states' => $states,
            'mode' => $mode,
            'enrollment' => $enrollment,
            'video' => VideoEmbed::parse($lesson->video_url),
            'contentHtml' => $lesson->content_text ? $this->markdown($lesson->content_text) : null,
            'instructionsHtml' => $lesson->assignment?->instructions ? $this->markdown($lesson->assignment->instructions) : null,
            'prev' => $prev && ($mode !== 'preview') ? $prev : null,
            'next' => $next && ($mode !== 'preview') && ($mode === 'owner' || $isCompleted) ? $next : null,
            'isCompleted' => $isCompleted,
            'canComplete' => $mode === 'student' && ! $isCompleted && ! $lesson->assignment,
            'submissions' => $submissions,
            'canSubmit' => $mode === 'student' && ! $isCompleted && $lesson->assignment
                && $this->submissions->canSubmit($submissions->first()),
            'position' => ['current' => $index + 1, 'total' => $lessons->count()],
        ]);
    }

    public function complete(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $user = $request->user();
        $this->ensureLessonInCourse($course, $lesson);
        $enrollment = $this->paidEnrollment($request, $course);
        abort_unless($enrollment, 403);

        $lessons = $this->progress->orderedLessons($course);
        $states = $this->progress->lessonStates($lessons, $this->progress->completedLessonIds($user, $course));

        if (($states[$lesson->id] ?? 'locked') === 'locked') {
            return back()->withErrors(['lesson' => 'Materi ini masih terkunci.']);
        }

        if ($lesson->assignment()->exists()) {
            return back()->withErrors(['lesson' => 'Materi ini baru selesai setelah tugasnya disetujui mentor.']);
        }

        $this->progress->markCompleted($user, $lesson, $enrollment);

        $lessons = $this->progress->orderedLessons($course);
        $states = $this->progress->lessonStates($lessons, $this->progress->completedLessonIds($user, $course));
        $next = $this->progress->nextLesson($lessons, $states);

        if ($next) {
            return redirect()->route('learn.show', [$course, $next])->with('status', 'Materi selesai. Lanjut ke materi berikutnya!');
        }

        return redirect()->route('learn.show', [$course, $lesson])
            ->with('status', $enrollment->fresh()->isCompleted()
                ? 'Selamat! Anda telah menyelesaikan seluruh materi kelas ini. 🎉'
                : 'Materi selesai. Tugas berikutnya menunggu penilaian mentor.');
    }

    private function paidEnrollment(Request $request, Course $course): ?Enrollment
    {
        return Enrollment::where('student_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->with('student')
            ->first();
    }

    private function ensureLessonInCourse(Course $course, Lesson $lesson): void
    {
        abort_unless($lesson->module()->where('course_id', $course->id)->exists(), 404);
    }

    private function markdown(string $text): string
    {
        return Str::markdown($text, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }
}
