<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mentor\CourseRequest;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = $request->user()->courses()
            ->withCount(['modules', 'lessons', 'enrollments'])
            ->latest()
            ->paginate(10);

        return view('mentor.courses.index', compact('courses'));
    }

    public function create(): View
    {
        return view('mentor.courses.form', ['course' => new Course(['level' => 'basic', 'price' => 0])]);
    }

    public function store(CourseRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail']);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['mentor_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_url'] = Storage::disk('public')->url(
                $request->file('thumbnail')->store('thumbnails', 'public')
            );
        }

        $course = Course::create($data);

        return redirect()->route('mentor.courses.show', $course)
            ->with('status', 'Kelas berhasil dibuat. Sekarang tambahkan bab dan materi.');
    }

    public function show(Course $course): View
    {
        Gate::authorize('manage', $course);

        $course->load(['modules.lessons.assignment'])->loadCount('enrollments');

        return view('mentor.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        Gate::authorize('manage', $course);

        return view('mentor.courses.form', compact('course'));
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $data = $request->safe()->except(['thumbnail']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('thumbnail')) {
            $this->deleteThumbnail($course);
            $data['thumbnail_url'] = Storage::disk('public')->url(
                $request->file('thumbnail')->store('thumbnails', 'public')
            );
        }

        $course->update($data);

        return redirect()->route('mentor.courses.show', $course)->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('manage', $course);

        if ($course->enrollments()->exists()) {
            return back()->withErrors(['course' => 'Kelas yang sudah memiliki siswa tidak dapat dihapus. Ubah menjadi draft saja.']);
        }

        $this->deleteThumbnail($course);
        $course->delete();

        return redirect()->route('mentor.courses.index')->with('status', 'Kelas berhasil dihapus.');
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'kelas';
        $slug = $base;
        $i = 2;
        while (Course::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function deleteThumbnail(Course $course): void
    {
        $prefix = Storage::disk('public')->url('');
        if ($course->thumbnail_url && str_starts_with($course->thumbnail_url, $prefix)) {
            Storage::disk('public')->delete(Str::after($course->thumbnail_url, $prefix));
        }
    }
}
