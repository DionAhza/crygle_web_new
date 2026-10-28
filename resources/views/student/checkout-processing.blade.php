@use('App\Support\Format')
<x-site-layout title="Memproses Pembayaran">
    <div class="mx-auto max-w-md"
         x-data="paymentPoll(@js(route('student.checkout.status', $enrollment)), @js(route('student.checkout.success', $enrollment)))"
         x-init="start()">
        <section class="rounded-3xl border border-blue-100 bg-white px-6 py-10 text-center shadow-sm shadow-blue-900/5 sm:px-10">
            {{-- Ikon --}}
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full border-2 text-brand"
                 :class="state === 'failed' ? 'border-rose-500 text-rose-500' : 'border-brand'">
                <svg x-show="state !== 'failed'" class="h-8 w-8" :class="state === 'processing' && 'animate-pulse'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                <svg x-show="state === 'failed'" x-cloak class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </div>

            <template x-if="state === 'processing'">
                <div>
                    <h1 class="mt-7 text-2xl font-extrabold leading-snug text-slate-800">Memproses pembayaran kamu...</h1>
                    <p class="mx-auto mt-3 max-w-xs text-xs leading-relaxed text-slate-500">Mohon tunggu sebentar, kami sedang memastikan transaksi kamu berjalan aman.</p>
                </div>
            </template>
            <template x-if="state === 'failed'">
                <div>
                    <h1 class="mt-7 text-2xl font-extrabold leading-snug text-slate-800">Pembayaran tidak berhasil</h1>
                    <p class="mx-auto mt-3 max-w-xs text-xs leading-relaxed text-slate-500">Transaksi dibatalkan atau sudah kedaluwarsa. Kamu bisa mencoba membayar lagi.</p>
                    <a href="{{ route('student.checkout', $enrollment) }}" class="mt-6 inline-block rounded-full bg-brand px-8 py-3 text-xs font-semibold text-white shadow-lg shadow-brand/30 hover:bg-brand-dark">Coba Bayar Lagi</a>
                </div>
            </template>
            <template x-if="state === 'timeout'">
                <div>
                    <h1 class="mt-7 text-2xl font-extrabold leading-snug text-slate-800">Menunggu konfirmasi</h1>
                    <p class="mx-auto mt-3 max-w-xs text-xs leading-relaxed text-slate-500">Pembayaran belum terkonfirmasi. Jika kamu sudah membayar, kelas akan aktif otomatis begitu pembayaran diterima.</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <button type="button" @click="start()" class="rounded-full bg-brand px-6 py-3 text-xs font-semibold text-white hover:bg-brand-dark">Cek Lagi</button>
                        <a href="{{ route('student.courses') }}" class="rounded-full border border-brand px-6 py-3 text-xs font-semibold text-brand hover:bg-brand-50">Kelas Saya</a>
                    </div>
                </div>
            </template>

            {{-- Ringkasan --}}
            <dl class="mt-8 space-y-2.5 rounded-2xl bg-[#f1f6fc] px-5 py-4 text-left text-[11px]">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Merchant</dt><dd class="font-bold text-slate-800">CRYGLE Academy</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Order ID</dt><dd class="font-bold text-slate-800">{{ $orderId }}</dd></div>
                <div class="flex justify-between gap-4 border-t border-blue-100 pt-2.5"><dt class="text-slate-500">Total</dt><dd class="text-sm font-extrabold text-brand">{{ Format::rupiah($enrollment->course->price) }}</dd></div>
            </dl>

            <p class="mt-6 flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                Secured by Encrypted Gateway
            </p>
        </section>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('paymentPoll', (statusUrl, successUrl) => ({
                state: 'processing',
                tries: 0,
                timer: null,
                start() {
                    this.state = 'processing';
                    this.tries = 0;
                    clearTimeout(this.timer);
                    this.check();
                },
                async check() {
                    try {
                        const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        if (data.paid) { window.location = successUrl; return; }
                        if (data.status === 'failed') { this.state = 'failed'; return; }
                    } catch (e) { /* coba lagi */ }
                    if (++this.tries >= 40) { this.state = 'timeout'; return; }
                    this.timer = setTimeout(() => this.check(), 3000);
                },
            }));
        });
    </script>
</x-site-layout>
