<x-panel-layout title="Tipe Mentor" heading="Tipe Mentor" subtitle="Spesialisasi mentor bersifat dinamis — tambah atau ubah sesuai kebutuhan." width="max-w-3xl">
    <x-flash />

    {{-- Tambah tipe --}}
    <x-card>
        <form method="POST" action="{{ route('admin.mentor-types.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            @csrf
            <x-text-input name="name" :value="old('name')" placeholder="Nama tipe baru, mis. UI/UX" required maxlength="80" class="flex-1" aria-label="Nama tipe baru" />
            <x-btn>+ Tambah Tipe</x-btn>
        </form>
    </x-card>

    {{-- Daftar tipe --}}
    <x-card padding="p-0" class="mt-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h2 class="text-sm font-bold text-slate-800">Daftar Tipe</h2>
            <x-badge tone="brand">{{ $types->count() }} tipe</x-badge>
        </div>

        @forelse ($types as $type)
            <div class="flex flex-wrap items-center gap-3 border-b border-slate-50 px-6 py-4 last:border-0">
                <form method="POST" action="{{ route('admin.mentor-types.update', $type) }}" class="flex min-w-[220px] flex-1 items-center gap-2">
                    @csrf @method('PATCH')
                    <x-text-input name="name" :value="$type->name" required maxlength="80" class="!h-10 flex-1" aria-label="Nama tipe" />
                    <x-btn variant="soft" size="sm">Simpan</x-btn>
                </form>

                <x-badge tone="slate">{{ $type->mentors_count }} mentor</x-badge>

                <form method="POST" action="{{ route('admin.mentor-types.destroy', $type) }}" onsubmit="return confirm('Hapus tipe mentor ini?')">
                    @csrf @method('DELETE')
                    <x-btn variant="danger-soft" size="sm">Hapus</x-btn>
                </form>
            </div>
        @empty
            <x-empty-state title="Belum ada tipe mentor" description="Tambahkan tipe pertama lewat kolom di atas, mis. WebDev atau UI/UX."
                           icon="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        @endforelse
    </x-card>
</x-panel-layout>
