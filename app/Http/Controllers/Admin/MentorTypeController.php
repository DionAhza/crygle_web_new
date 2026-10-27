<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MentorTypeRequest;
use App\Models\MentorType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MentorTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.mentor-types.index', [
            'types' => MentorType::withCount('mentors')->orderBy('name')->get(),
        ]);
    }

    public function store(MentorTypeRequest $request): RedirectResponse
    {
        $name = trim($request->validated('name'));
        MentorType::create(['name' => $name, 'slug' => MentorType::uniqueSlug($name)]);

        return back()->with('status', "Tipe mentor \"{$name}\" berhasil ditambahkan.");
    }

    public function update(MentorTypeRequest $request, MentorType $mentorType): RedirectResponse
    {
        $name = trim($request->validated('name'));
        $mentorType->update(['name' => $name, 'slug' => MentorType::uniqueSlug($name, $mentorType->id)]);

        return back()->with('status', 'Tipe mentor berhasil diperbarui.');
    }

    public function destroy(MentorType $mentorType): RedirectResponse
    {
        $count = $mentorType->mentors()->count();

        if ($count > 0) {
            return back()->withErrors(['mentor_type' => "Tipe \"{$mentorType->name}\" masih dipakai {$count} mentor. Pindahkan mentornya ke tipe lain terlebih dahulu."]);
        }

        $mentorType->delete();

        return back()->with('status', 'Tipe mentor berhasil dihapus.');
    }
}
