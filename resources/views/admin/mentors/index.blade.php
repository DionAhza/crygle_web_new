<x-panel-layout title="Akun Mentor" heading="Akun Mentor" subtitle="Akun mentor hanya dapat dibuat oleh admin." width="max-w-5xl">
    <x-slot name="actions">
        <x-btn :href="route('admin.mentors.create')">+ Mentor Baru</x-btn>
    </x-slot>

    <x-flash />

    {{-- Pencarian & filter --}}
    <form method="GET" class="mb-5 grid gap-3 sm:grid-cols-[1fr_220px_auto]">
        <div class="relative">
            <svg class="pointer-events-none absolute start-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M20 20l-3.5-3.5"/></svg>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau email…"
                   class="h-11 w-full rounded-xl border border-slate-200 bg-white ps-11 pe-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand focus:ring-brand">
        </div>
        <x-select name="type" class="!rounded-xl !border-slate-200" aria-label="Filter tipe">
            <option value="">Semua tipe</option>
            @foreach ($types as $type)
                <option value="{{ $type->id }}" @selected((int) ($filters['type'] ?? 0) === $type->id)>{{ $type->name }}</option>
            @endforeach
        </x-select>
        <x-btn class="!h-11">Cari</x-btn>
    </form>

    <x-card padding="p-0" class="overflow-hidden">
        @forelse ($mentors as $mentor)
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-50 px-6 py-4 transition last:border-0 hover:bg-slate-50/60">
                <div class="flex min-w-0 items-center gap-4">
                    <x-avatar :name="$mentor->name" :src="$mentor->avatar_url" />
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="truncate font-semibold text-slate-800">{{ $mentor->name }}</span>
                            @if ($mentor->mentorType)
                                <x-badge tone="brand">{{ $mentor->mentorType->name }}</x-badge>
                            @endif
                        </div>
                        <div class="truncate text-xs text-slate-500">{{ $mentor->email }} · {{ $mentor->courses_count }} kelas</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-btn :href="route('admin.mentors.edit', $mentor)" variant="secondary" size="sm">Edit</x-btn>
                    <form method="POST" action="{{ route('admin.mentors.destroy', $mentor) }}" onsubmit="return confirm('Hapus akun mentor ini?')">
                        @csrf @method('DELETE')
                        <x-btn variant="danger-soft" size="sm">Hapus</x-btn>
                    </form>
                </div>
            </div>
        @empty
            <x-empty-state title="Belum ada mentor yang cocok" description="Coba ubah kata kunci pencarian, atau daftarkan mentor baru.">
                <x-btn :href="route('admin.mentors.create')" size="sm">+ Mentor Baru</x-btn>
            </x-empty-state>
        @endforelse
    </x-card>

    <div class="mt-6">{{ $mentors->links() }}</div>
</x-panel-layout>
