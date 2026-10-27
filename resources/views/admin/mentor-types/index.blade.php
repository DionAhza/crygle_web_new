<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Tipe Mentor</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <form method="POST" action="{{ route('admin.mentor-types.store') }}" class="flex gap-2 rounded-xl border border-dashed border-slate-300 bg-white p-4">
                @csrf
                <input name="name" value="{{ old('name') }}" placeholder="Nama tipe baru, mis. UI/UX" required maxlength="80" class="flex-1 border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button class="rounded-full bg-[#2D68A8] px-5 py-2 text-sm font-semibold text-white hover:bg-blue-800">+ Tambah</button>
            </form>

            <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                @forelse ($types as $type)
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-50 px-4 py-3 last:border-0">
                        <form method="POST" action="{{ route('admin.mentor-types.update', $type) }}" class="flex flex-1 items-center gap-2">
                            @csrf @method('PATCH')
                            <input name="name" value="{{ $type->name }}" required maxlength="80" class="flex-1 border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button class="text-xs font-semibold text-indigo-600 hover:underline">Simpan</button>
                        </form>
                        <span class="text-xs text-slate-500">{{ $type->mentors_count }} mentor</span>
                        <form method="POST" action="{{ route('admin.mentor-types.destroy', $type) }}" onsubmit="return confirm('Hapus tipe mentor ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-rose-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500">Belum ada tipe mentor. Tambahkan tipe pertama di atas.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
