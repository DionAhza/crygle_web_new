<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Dashboard Admin</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 px-4 sm:px-0">
                @foreach ([['Mentor', $stats['mentors']], ['Siswa', $stats['students']], ['Kelas', $stats['courses']], ['Tipe Mentor', $stats['mentor_types']]] as [$label, $value])
                    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
                        <div class="text-sm text-slate-500">{{ $label }}</div>
                        <div class="mt-1 text-3xl font-bold text-indigo-700">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-4 sm:grid-cols-2 px-4 sm:px-0">
                <a href="{{ route('admin.mentors.index') }}" class="rounded-xl border border-slate-100 bg-white p-6 shadow-sm hover:shadow-md">
                    <div class="font-semibold text-slate-800">Kelola Akun Mentor</div>
                    <p class="mt-1 text-sm text-slate-500">Daftarkan mentor baru dan tetapkan tipe mentornya.</p>
                </a>
                <a href="{{ route('admin.mentor-types.index') }}" class="rounded-xl border border-slate-100 bg-white p-6 shadow-sm hover:shadow-md">
                    <div class="font-semibold text-slate-800">Kelola Tipe Mentor</div>
                    <p class="mt-1 text-sm text-slate-500">Tambah, ubah, atau hapus spesialisasi mentor (3D, UI/UX, WebDev, dll.).</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
