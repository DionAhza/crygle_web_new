@use('App\Support\Format')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Pembayaran</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            @if ($error)
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $error }}</div>
            @endif

            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400">Kelas</div>
                    <div class="font-semibold text-slate-800">{{ $enrollment->course->title }}</div>
                    <div class="text-sm text-slate-500">oleh {{ $enrollment->course->mentor->name }}</div>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                    <span class="text-sm text-slate-500">Total</span>
                    <span class="text-2xl font-extrabold text-indigo-700">{{ Format::rupiah($enrollment->course->price) }}</span>
                </div>

                @if ($snapToken)
                    <button id="pay-button" class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Bayar dengan Midtrans</button>
                    <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
                    <script>
                        document.getElementById('pay-button').addEventListener('click', () => {
                            window.snap.pay(@js($snapToken), {
                                onSuccess: () => window.location = @js(route('student.courses')),
                                onPending: () => window.location = @js(route('student.courses')),
                                onError: () => alert('Pembayaran gagal. Silakan coba lagi.'),
                            });
                        });
                    </script>
                    <p class="text-xs text-slate-500">Status kelas akan aktif otomatis setelah pembayaran terkonfirmasi.</p>
                @elseif ($canSimulate)
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600">
                        Kunci Midtrans belum diisi (mode pengembangan lokal).
                    </div>
                    <form method="POST" action="{{ route('student.checkout.simulate', $enrollment) }}">
                        @csrf
                        <button class="w-full rounded-lg bg-slate-800 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-700">Simulasikan Pembayaran Berhasil</button>
                    </form>
                @elseif (! $error)
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Pembayaran online belum tersedia. Silakan hubungi admin.</p>
                @endif

                <a href="{{ route('student.courses') }}" class="block text-center text-sm text-slate-500 hover:underline">Kembali ke Kelas Saya</a>
            </div>
        </div>
    </div>
</x-app-layout>
