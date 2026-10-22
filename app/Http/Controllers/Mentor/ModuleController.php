<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ModuleController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $data = $request->validate(['title' => ['required', 'string', 'max:150']]);

        $course->modules()->create([
            'title' => $data['title'],
            'sort_order' => ((int) $course->modules()->max('sort_order')) + 1,
        ]);

        return back()->with('status', 'Bab berhasil ditambahkan.');
    }

    public function update(Request $request, Module $module): RedirectResponse
    {
        Gate::authorize('manage', $module->course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $module->update(array_filter($data, fn ($v) => $v !== null));

        return back()->with('status', 'Bab berhasil diperbarui.');
    }

    public function destroy(Module $module): RedirectResponse
    {
        Gate::authorize('manage', $module->course);

        if ($module->course->enrollments()->exists()) {
            return back()->withErrors(['module' => 'Bab tidak dapat dihapus karena kelas sudah memiliki siswa.']);
        }

        $module->delete();

        return back()->with('status', 'Bab berhasil dihapus.');
    }
}
