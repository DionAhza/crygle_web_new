<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MentorRequest;
use App\Models\MentorType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class MentorController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'integer'],
        ]);

        $mentors = User::role('mentor')
            ->with('mentorType:id,name')
            ->withCount('courses')
            ->when($data['q'] ?? null, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.addcslashes($term, '%_\\').'%')
                ->orWhere('email', 'like', '%'.addcslashes($term, '%_\\').'%')))
            ->when($data['type'] ?? null, fn ($q, $type) => $q->where('mentor_type_id', $type))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.mentors.index', [
            'mentors' => $mentors,
            'types' => MentorType::orderBy('name')->get(),
            'filters' => $data,
        ]);
    }

    public function create(): View
    {
        return view('admin.mentors.form', ['mentor' => new User, 'types' => MentorType::orderBy('name')->get()]);
    }

    public function store(MentorRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $mentor = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'mentor_type_id' => $data['mentor_type_id'],
        ]);
        // Akun dibuat Admin sehingga alamat emailnya dianggap sudah terverifikasi.
        $mentor->forceFill(['email_verified_at' => now()])->save();
        $mentor->assignRole(Role::findOrCreate('mentor', 'web'));

        return redirect()->route('admin.mentors.index')->with('status', "Akun mentor {$mentor->name} berhasil dibuat.");
    }

    public function edit(User $mentor): View
    {
        $this->ensureMentor($mentor);

        return view('admin.mentors.form', ['mentor' => $mentor, 'types' => MentorType::orderBy('name')->get()]);
    }

    public function update(MentorRequest $request, User $mentor): RedirectResponse
    {
        $this->ensureMentor($mentor);
        $data = $request->validated();

        $mentor->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'mentor_type_id' => $data['mentor_type_id'],
        ]);
        if (! empty($data['password'])) {
            $mentor->password = $data['password'];
        }
        $mentor->save();

        return redirect()->route('admin.mentors.index')->with('status', 'Data mentor berhasil diperbarui.');
    }

    public function destroy(User $mentor): RedirectResponse
    {
        $this->ensureMentor($mentor);

        // Menghapus mentor akan ikut menghapus kelas, siswa terdaftar, dan tugasnya (cascade), jadi ditolak.
        if ($mentor->courses()->exists()) {
            return back()->withErrors(['mentor' => "Mentor {$mentor->name} masih memiliki kelas dan tidak dapat dihapus."]);
        }

        $mentor->delete();

        return back()->with('status', 'Akun mentor berhasil dihapus.');
    }

    private function ensureMentor(User $user): void
    {
        abort_unless($user->hasRole('mentor'), 404);
    }
}
