@use('App\Support\Format')
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Kelas Saya</h2>
            <a href="{{ route('catalog.index') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Jelajahi Katalog</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <x-flash />

            @if ($unpaid->isNotEmpty())
                <section class="px-4 sm:px-0">
                    <h3 class="mb-3 font-semibold text-slate-800">Menunggu Pembayaran</h3>
                    <div class="space-y-2">
                        @foreach ($unpaid as $enrollment)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <div>
                                    <div class="font-medium text-slate-800">{{ $enrollment->course->title }}</div>
                                    <div class="text-xs text-slate-500">{{ Format::rupiah($enrollment->course->price) }} · {{ $enrollment->payment_status === 'failed' ? 'Pembayaran gagal/kedaluwarsa' : 'Belum dibayar' }}</div>
                                </div>
                                <a href="{{ route('student.checkout', $enrollment) }}" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">Bayar Sekarang</a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section>
                <h3 class="mb-3 px-4 font-semibold text-slate-800 sm:px-0">Sedang Dipelajari</h3>
                <div class="grid gap-4 px-4 sm:grid-cols-2 sm:px-0 lg:grid-cols-3">
                    @forelse ($active as $enrollment)
                        <div class="flex flex-col overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                            @if ($enrollment->course->thumbnail_url)
                                <img src="{{ $enrollment->course->thumbnail_url }}" alt="" class="h-32 w-full object-cover">
                            @else
                                <div class="h-32 bg-gradient-to-br from-indigo-500 to-slate-700"></div>
                            @endif
                            <div class="flex flex-1 flex-col p-5">
                                <h4 class="font-semibold text-slate-800">{{ $enrollment->course->title }}</h4>
                                <p class="text-xs text-slate-500">oleh {{ $enrollment->course->mentor->name }}</p>
                                <div class="mt-4">
                                    <div class="mb-1 flex justify-between text-xs text-slate-500"><span>Progres</span><span>{{ $enrollment->progress_percent }}%</span></div>
                                    <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-indigo-600" style="width: {{ $enrollment->progress_percent }}%"></div></div>
                                </div>
                                <a href="{{ route('catalog.show', $enrollment->course) }}" class="mt-4 rounded-lg border border-slate-200 px-3 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Lihat Kelas</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Anda belum mengikuti kelas apa pun. <a href="{{ route('catalog.index') }}" class="font-semibold text-indigo-600 hover:underline">Cari kelas</a> untuk memulai.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
