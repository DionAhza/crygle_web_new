<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Akun Mentor</h2>
            <a href="{{ route('admin.mentors.create') }}" class="rounded-full bg-[#2D68A8] px-5 py-2 text-sm font-semibold text-white hover:bg-blue-800">+ Mentor Baru</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-flash />

            <form method="GET" class="mb-4 grid gap-3 px-4 sm:grid-cols-[1fr_auto_auto] sm:px-0">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau email…" class="rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <select name="type" class="rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua tipe</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" @selected((int) ($filters['type'] ?? 0) === $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-slate-800 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-700">Cari</button>
            </form>

            <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                @forelse ($mentors as $mentor)
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-50 px-5 py-4 last:border-0">
                        <div>
                            <div class="font-medium text-slate-800">{{ $mentor->name }}
                                @if ($mentor->mentorType)<span class="ms-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $mentor->mentorType->name }}</span>@endif
                            </div>
                            <div class="text-xs text-slate-500">{{ $mentor->email }} · {{ $mentor->courses_count }} kelas</div>
                        </div>
                        <div class="flex items-center gap-4 text-xs">
                            <a href="{{ route('admin.mentors.edit', $mentor) }}" class="font-semibold text-indigo-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.mentors.destroy', $mentor) }}" onsubmit="return confirm('Hapus akun mentor ini?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500">Belum ada mentor yang cocok.</p>
                @endforelse
            </div>
            <div class="mt-6 px-4 sm:px-0">{{ $mentors->links() }}</div>
        </div>
    </div>
</x-app-layout>
