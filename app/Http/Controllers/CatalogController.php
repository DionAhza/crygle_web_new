<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'in:basic,intermediate,advanced'],
            'price' => ['nullable', 'in:free,paid'],
        ]);

        $courses = Course::published()
            ->with('mentor:id,name')
            ->withCount(['modules', 'lessons'])
            ->withSum('lessons', 'duration_seconds')
            ->when($data['q'] ?? null, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('title', 'like', '%'.addcslashes($term, '%_\\').'%')
                ->orWhere('description', 'like', '%'.addcslashes($term, '%_\\').'%')))
            ->when($data['level'] ?? null, fn ($q, $level) => $q->where('level', $level))
            ->when(($data['price'] ?? null) === 'free', fn ($q) => $q->where('price', '<=', 0))
            ->when(($data['price'] ?? null) === 'paid', fn ($q) => $q->where('price', '>', 0))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('catalog.index', ['courses' => $courses, 'filters' => $data]);
    }

    public function show(Request $request, Course $course): View
    {
        $user = $request->user();

        // Kelas draft hanya boleh dipratinjau oleh mentor pemiliknya.
        abort_unless($course->is_published || ($user && $user->id === $course->mentor_id), 404);

        $course->load(['mentor:id,name,mentor_type_id', 'mentor.mentorType:id,name', 'modules.lessons:id,module_id,title,duration_seconds,sort_order,is_free_preview']);

        $enrollment = $user?->hasRole('student')
            ? Enrollment::where('student_id', $user->id)->where('course_id', $course->id)->first()
            : null;

        // Kelas serupa: prioritaskan level yang sama, lalu yang terbaru.
        $similar = Course::published()
            ->where('id', '!=', $course->id)
            ->with('mentor:id,name')
            ->orderByRaw('case when level = ? then 0 else 1 end', [$course->level])
            ->latest()
            ->limit(3)
            ->get();

        return view('catalog.show', [
            'similar' => $similar,
            'course' => $course,
            'enrollment' => $enrollment,
            'lessonCount' => $course->modules->sum(fn ($m) => $m->lessons->count()),
            'totalSeconds' => $course->modules->sum(fn ($m) => $m->lessons->sum('duration_seconds')),
        ]);
    }
}
