<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Dashboard Mentor</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 px-4 sm:px-0">
                @foreach ([['Total Kelas', $stats['courses']], ['Kelas Terbit', $stats['published']], ['Total Siswa', $stats['students']], ['Tugas Menunggu Review', $stats['pending']]] as [$label, $value])
                    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
                        <div class="text-sm text-slate-500">{{ $label }}</div>
                        <div class="mt-1 text-3xl font-bold text-indigo-700">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl bg-white shadow-sm border border-slate-100">
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800">Kelas Terbaru</h3>
                    <a href="{{ route('mentor.courses.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">+ Kelas Baru</a>
                </div>
                @forelse ($courses->take(5) as $course)
                    <a href="{{ route('mentor.courses.show', $course) }}" class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 border-b border-slate-50 last:border-0">
                        <div>
                            <div class="font-medium text-slate-800">{{ $course->title }}</div>
                            <div class="text-xs text-slate-500">{{ $course->modules_count }} bab · {{ $course->lessons_count }} materi · {{ $course->enrollments_count }} siswa</div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $course->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $course->is_published ? 'Terbit' : 'Draft' }}
                        </span>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-500">Belum ada kelas. Mulai dengan membuat kelas pertama Anda.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
