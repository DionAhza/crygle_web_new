<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mentor\LessonRequest;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function create(Module $module): View
    {
        Gate::authorize('manage', $module->course);

        return view('mentor.lessons.form', [
            'module' => $module,
            'lesson' => new Lesson(['duration_seconds' => 0]),
        ]);
    }

    public function store(LessonRequest $request, Module $module): RedirectResponse
    {
        Gate::authorize('manage', $module->course);

        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($module, $data['title']);
        $data['sort_order'] = ((int) $module->lessons()->max('sort_order')) + 1;
        $data['duration_seconds'] ??= 0;
        $data['is_free_preview'] = $request->boolean('is_free_preview');

        $module->lessons()->create($data);

        return redirect()->route('mentor.courses.show', $module->course)->with('status', 'Materi berhasil ditambahkan.');
    }

    public function edit(Lesson $lesson): View
    {
        $module = $lesson->module->load('course');
        Gate::authorize('manage', $module->course);

        return view('mentor.lessons.form', compact('lesson', 'module'));
    }

    public function update(LessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $module = $lesson->module->load('course');
        Gate::authorize('manage', $module->course);

        $data = $request->validated();
        $data['duration_seconds'] ??= 0;
        $data['is_free_preview'] = $request->boolean('is_free_preview');
        if (($data['sort_order'] ?? null) === null) {
            unset($data['sort_order']);
        }

        $lesson->update($data);

        return redirect()->route('mentor.courses.show', $module->course)->with('status', 'Materi berhasil diperbarui.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $module = $lesson->module->load('course');
        Gate::authorize('manage', $module->course);

        if ($module->course->enrollments()->exists()) {
            return back()->withErrors(['lesson' => 'Materi tidak dapat dihapus karena kelas sudah memiliki siswa.']);
        }

        $lesson->delete();

        return back()->with('status', 'Materi berhasil dihapus.');
    }

    private function uniqueSlug(Module $module, string $title): string
    {
        $base = Str::slug($title) ?: 'materi';
        $slug = $base;
        $i = 2;
        while ($module->lessons()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
