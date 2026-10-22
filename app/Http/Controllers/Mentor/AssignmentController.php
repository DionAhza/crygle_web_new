<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mentor\AssignmentRequest;
use App\Models\Assignment;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function create(Lesson $lesson): View|RedirectResponse
    {
        $course = $lesson->module->course;
        Gate::authorize('manage', $course);

        if ($lesson->assignment) {
            return redirect()->route('mentor.assignments.edit', $lesson->assignment);
        }

        return view('mentor.assignments.form', [
            'lesson' => $lesson,
            'course' => $course,
            'assignment' => new Assignment([
                'submission_type' => 'repo_link',
                'passing_score' => config('crygle.default_passing_score'),
            ]),
        ]);
    }

    public function store(AssignmentRequest $request, Lesson $lesson): RedirectResponse
    {
        $course = $lesson->module->course;
        Gate::authorize('manage', $course);

        if ($lesson->assignment()->exists()) {
            return back()->withErrors(['assignment' => 'Materi ini sudah memiliki tugas.']);
        }

        $lesson->assignment()->create($request->validated());

        return redirect()->route('mentor.courses.show', $course)->with('status', 'Tugas berhasil dibuat.');
    }

    public function edit(Assignment $assignment): View
    {
        $lesson = $assignment->lesson->load('module.course');
        Gate::authorize('manage', $lesson->module->course);

        return view('mentor.assignments.form', [
            'assignment' => $assignment,
            'lesson' => $lesson,
            'course' => $lesson->module->course,
        ]);
    }

    public function update(AssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $course = $assignment->lesson->module->course;
        Gate::authorize('manage', $course);

        $assignment->update($request->validated());

        return redirect()->route('mentor.courses.show', $course)->with('status', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        $course = $assignment->lesson->module->course;
        Gate::authorize('manage', $course);

        if ($assignment->submissions()->exists()) {
            return back()->withErrors(['assignment' => 'Tugas yang sudah dikumpulkan siswa tidak dapat dihapus.']);
        }

        $assignment->delete();

        return back()->with('status', 'Tugas berhasil dihapus.');
    }
}
