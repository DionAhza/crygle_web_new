@use('App\Support\Format')
<x-site-layout title="Kuitansi Pembayaran">
    <div class="mx-auto max-w-md">
        <section class="rounded-2xl border border-blue-100 bg-white p-7 shadow-sm shadow-blue-900/5 print:border-0 print:shadow-none">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-sm font-bold text-slate-900">Kuitansi Pembayaran</h1>
                    <p class="mt-0.5 text-[11px] text-slate-500">CRYGLE Academy</p>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Lunas</span>
            </div>

            <dl class="mt-6 space-y-3.5 text-xs">
                <div class="flex justify-between gap-6"><dt class="text-slate-500">Order ID</dt><dd class="text-right font-medium text-slate-800">{{ $orderId }}</dd></div>
                <div class="flex justify-between gap-6"><dt class="text-slate-500">Tanggal</dt><dd class="text-right font-medium text-slate-800">{{ $paidOn }}</dd></div>
                <div class="flex justify-between gap-6"><dt class="text-slate-500">Atas Nama</dt><dd class="text-right font-medium text-slate-800">{{ $enrollment->student->name }}</dd></div>
                <div class="flex justify-between gap-6"><dt class="text-slate-500">Course</dt><dd class="text-right font-medium text-slate-800">{{ $enrollment->course->title }}</dd></div>
                <div class="flex justify-between gap-6"><dt class="text-slate-500">Mentor</dt><dd class="text-right font-medium text-slate-800">{{ $enrollment->course->mentor->name }}</dd></div>
            </dl>

            <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-5">
                <span class="text-xs text-slate-500">Total Invest</span>
                <span class="text-xl font-extrabold text-brand">{{ Format::rupiah($enrollment->course->price) }}</span>
            </div>
        </section>

        <div class="mt-6 flex justify-center gap-3 print:hidden">
            <button type="button" onclick="window.print()" class="rounded-full bg-brand px-8 py-3 text-xs font-semibold text-white shadow-lg shadow-brand/30 hover:bg-brand-dark">Cetak / Simpan PDF</button>
            <a href="{{ route('student.checkout.success', $enrollment) }}" class="rounded-full border border-brand px-8 py-3 text-xs font-semibold text-brand hover:bg-brand-50">Kembali</a>
        </div>
    </div>
</x-site-layout>
