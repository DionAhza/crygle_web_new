@use('App\Support\Format')
<x-checkout-layout title="Pembayaran Berhasil">
    <div class="mx-auto max-w-xl text-center">
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-3xl bg-white shadow-lg shadow-blue-900/10">
            <span class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                </span>
            </span>
        </div>

        <h1 class="mt-8 text-3xl font-extrabold text-slate-800">Pembayaran Berhasil! 🎉</h1>
        <p class="mx-auto mt-3 max-w-sm text-xs leading-relaxed text-slate-500">Kamu sudah berhasil bergabung di kelas ini. Perjalanan belajarmu dimulai sekarang di sanctuary digital kami.</p>

        {{-- Kartu kelas --}}
        <div class="mt-8 rounded-2xl bg-[#eaf2fb] p-4 text-left sm:p-5">
            <div class="flex items-center gap-4">
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="" class="h-12 w-12 shrink-0 rounded-lg object-cover">
                @else
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-white text-brand">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                    </span>
                @endif
                <div class="min-w-0">
                    <div class="text-xs font-bold leading-snug text-slate-800">{{ $course->title }}</div>
                    <div class="mt-0.5 text-[10px] text-slate-500">Level {{ Format::level($course->level) }} · {{ $course->mentor->name }}</div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-between text-[11px]">
                <span class="font-medium text-slate-600">Progress Belajar</span>
                <span class="font-bold text-brand">{{ $enrollment->progress_percent }}%</span>
            </div>
            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-blue-200">
                <div class="h-full rounded-full bg-brand" style="width: {{ max(2, $enrollment->progress_percent) }}%"></div>
            </div>
            <p class="mt-2 text-center text-[10px] text-slate-400">{{ $enrollment->progress_percent > 0 ? 'Lanjutkan belajarmu dari materi terakhir.' : 'Belum ada materi yang diselesaikan' }}</p>
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ $startUrl }}" class="rounded-full bg-brand px-10 py-3 text-xs font-semibold text-white shadow-lg shadow-brand/30 transition hover:bg-brand-dark">Mulai Belajar</a>
            <a href="{{ route('student.checkout.receipt', $enrollment) }}" class="rounded-full border border-brand px-10 py-3 text-xs font-semibold text-brand transition hover:bg-brand-50">Lihat Kuitansi</a>
        </div>

        <ul class="mx-auto mt-10 flex max-w-md items-start justify-between border-t border-slate-200 pt-6 text-[9px] font-semibold uppercase tracking-wider text-slate-400">
            @foreach ([
                ['Akses Selamanya', 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z'],
                ['Sertifikat Resmi', 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z'],
                ['Grup Komunitas', 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z'],
            ] as [$label, $icon])
                <li class="flex flex-1 flex-col items-center gap-2">
                    <svg class="h-4 w-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    {{ $label }}
                </li>
            @endforeach
        </ul>
    </div>
</x-checkout-layout>
