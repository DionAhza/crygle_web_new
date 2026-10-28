@use('App\Support\Format')
<x-checkout-layout title="Konfirmasi Pembayaran">
    <x-checkout-steps :active="2" />

    <div class="mx-auto mt-10 max-w-md">
        <x-flash />
        @if ($error)
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $error }}</div>
        @endif

        <section class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm shadow-blue-900/5 sm:p-7">
            <h1 class="text-center text-sm font-bold text-slate-900">Konfirmasi Pembayaran</h1>

            <dl class="mt-6 space-y-4 text-xs">
                <div class="flex items-start justify-between gap-6">
                    <dt class="text-slate-500">Atas Nama</dt>
                    <dd class="text-right font-medium text-slate-800">{{ $enrollment->student->name }}</dd>
                </div>
                <div class="flex items-start justify-between gap-6">
                    <dt class="text-slate-500">Course</dt>
                    <dd class="text-right font-medium text-slate-800">{{ $enrollment->course->title }}</dd>
                </div>
                <div class="flex items-start justify-between gap-6">
                    <dt class="text-slate-500">Mentor</dt>
                    <dd class="text-right font-medium text-slate-800">{{ $enrollment->course->mentor->name }}</dd>
                </div>
                <div class="flex items-start justify-between gap-6">
                    <dt class="text-slate-500">Metode Pembayaran</dt>
                    <dd class="text-right font-medium text-slate-800">{{ $snapToken ? 'Midtrans (transfer bank, e-wallet, QRIS)' : 'Belum tersedia' }}</dd>
                </div>
            </dl>

            <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-5">
                <span class="text-xs text-slate-500">Total Invest</span>
                <span class="text-xl font-extrabold text-brand">{{ Format::rupiah($enrollment->course->price) }}</span>
            </div>

            @if ($snapToken)
                <button id="pay-button" type="button" class="mt-5 w-full rounded-full bg-brand px-4 py-3.5 text-xs font-semibold text-white shadow-lg shadow-brand/30 transition hover:bg-brand-dark">Konfirmasi &amp; Bayar</button>
                <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
                <script>
                    (() => {
                        const finish = @js(route('student.checkout.finish', $enrollment));
                        const go = (r) => window.location = finish + (r && r.order_id ? '?order_id=' + encodeURIComponent(r.order_id) : '');
                        document.getElementById('pay-button').addEventListener('click', () => {
                            window.snap.pay(@js($snapToken), {
                                onSuccess: go,
                                onPending: go,
                                onError: () => alert('Pembayaran gagal. Silakan coba lagi.'),
                            });
                        });
                    })();
                </script>
            @elseif ($canSimulate)
                <div class="mt-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3 text-xs text-slate-600">
                    Kunci Midtrans belum diisi (mode pengembangan lokal).
                </div>
                <form method="POST" action="{{ route('student.checkout.simulate', $enrollment) }}">
                    @csrf
                    <button class="mt-3 w-full rounded-full bg-brand px-4 py-3.5 text-xs font-semibold text-white shadow-lg shadow-brand/30 hover:bg-brand-dark">Konfirmasi &amp; Bayar (Simulasi)</button>
                </form>
            @elseif (! $error)
                <p class="mt-5 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">Pembayaran online belum tersedia. Silakan hubungi admin.</p>
            @endif

            <a href="{{ route('student.courses') }}" class="mt-4 block text-center text-xs text-slate-500 hover:text-brand hover:underline">Kembali ke Kelas Saya</a>
        </section>
    </div>
</x-checkout-layout>
