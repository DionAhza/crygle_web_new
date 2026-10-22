@use('App\Support\Format')
<x-public-layout :title="$course->title">
    @if (! $course->is_published)
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Pratinjau: kelas ini masih draft dan belum tampil di katalog.</div>
    @endif
    <x-flash />

    <div class="grid gap-8 lg:grid-cols-[1fr_340px]">
        <div>
            <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-blue-700 hover:underline">← Kembali ke katalog</a>
            <span class="mt-4 block w-fit rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold capitalize text-blue-700">{{ $course->level }}</span>
            <h1 class="mt-3 text-3xl font-extrabold leading-tight text-blue-800">{{ $course->title }}</h1>
            <p class="mt-2 text-sm text-slate-500">Mentor: <span class="font-semibold text-slate-700">{{ $course->mentor->name }}</span></p>

            @if ($course->description)
                <div class="prose prose-slate mt-6 max-w-none">{!! nl2br(e($course->description)) !!}</div>
            @endif

            <h2 class="mt-10 text-xl font-bold text-slate-800">Silabus</h2>
            <p class="text-sm text-slate-500">{{ $course->modules->count() }} bab · {{ $lessonCount }} materi · {{ Format::duration((int) $totalSeconds) }}</p>

            <div class="mt-4 space-y-3">
                @forelse ($course->modules as $module)
                    <details class="group rounded-xl border border-blue-100 bg-white" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer items-center justify-between px-5 py-4 font-semibold text-slate-800">
                            <span>{{ $module->sort_order }}. {{ $module->title }}</span>
                            <span class="text-xs font-normal text-slate-500">{{ $module->lessons->count() }} materi</span>
                        </summary>
                        <ul class="divide-y divide-blue-50 border-t border-blue-50">
                            @foreach ($module->lessons as $lesson)
                                <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                                    <span class="text-slate-700">{{ $lesson->title }}
                                        @if ($lesson->is_free_preview)<span class="ms-1 rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700">Preview</span>@endif
                                    </span>
                                    <span class="shrink-0 text-xs text-slate-400">{{ Format::duration($lesson->duration_seconds) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @empty
                    <p class="rounded-xl border border-dashed border-blue-200 bg-white p-6 text-sm text-slate-500">Silabus belum tersedia.</p>
                @endforelse
            </div>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="" class="h-44 w-full object-cover">
                @else
                    <div class="h-44 bg-gradient-to-br from-blue-700 to-blue-400"></div>
                @endif
                <div class="space-y-4 p-6">
                    <p class="text-3xl font-extrabold {{ $course->isFree() ? 'text-emerald-600' : 'text-blue-800' }}">{{ Format::rupiah($course->price) }}</p>

                    @guest
                        <a href="{{ route('login') }}" class="block rounded-full bg-blue-800 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-blue-900">Masuk untuk Mendaftar</a>
                        <p class="text-center text-xs text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-blue-700 hover:underline">Daftar</a></p>
                    @else
                        @if (auth()->user()->hasRole('student'))
                            @if ($enrollment?->isPaid())
                                <a href="{{ route('student.courses') }}" class="block rounded-full bg-emerald-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-emerald-700">Anda Sudah Terdaftar</a>
                            @elseif ($enrollment)
                                <a href="{{ route('student.checkout', $enrollment) }}" class="block rounded-full bg-amber-500 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-amber-600">Selesaikan Pembayaran</a>
                            @elseif ($course->is_published)
                                <form method="POST" action="{{ route('student.enroll', $course) }}">
                                    @csrf
                                    <button class="w-full rounded-full bg-blue-800 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-900">{{ $course->isFree() ? 'Daftar Gratis' : 'Beli Kelas' }}</button>
                                </form>
                            @endif
                        @else
                            <p class="rounded-lg bg-slate-50 p-3 text-center text-xs text-slate-500">Hanya akun siswa yang dapat mendaftar kelas.</p>
                        @endif
                    @endguest

                    <ul class="space-y-1 border-t border-blue-50 pt-4 text-sm text-slate-600">
                        <li>📚 {{ $lessonCount }} materi video &amp; bacaan</li>
                        <li>⏱ {{ Format::duration((int) $totalSeconds) }} total durasi</li>
                        <li>📝 Tugas dengan review mentor</li>
                        <li>🎓 Sertifikat setelah selesai</li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</x-public-layout>
