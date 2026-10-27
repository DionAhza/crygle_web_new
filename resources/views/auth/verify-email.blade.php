<x-auth-layout title="Verifikasi Email">
    <h1 class="mt-10 text-2xl font-bold tracking-tight">Verifikasi Email Kamu ✉️</h1>
    <p class="mt-2 text-xs leading-relaxed text-slate-500">
        Terima kasih sudah mendaftar! Sebelum mulai belajar, klik tautan verifikasi yang kami kirim ke
        <span class="font-semibold text-slate-700">{{ auth()->user()->email }}</span>.
        Belum menerima emailnya? Cek folder spam atau kirim ulang di bawah.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 rounded-lg bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
            Tautan verifikasi baru sudah dikirim ke alamat email Anda.
        </div>
    @endif

    @if ($devUrl)
        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800">
            <p class="font-semibold">Mode pengembangan</p>
            <p class="mt-1">Aplikasi memakai <code>MAIL_MAILER={{ $mailer }}</code>, sehingga email <b>tidak dikirim ke inbox</b> melainkan hanya ditulis ke <code>storage/logs/laravel.log</code>. Isi pengaturan SMTP di <code>.env</code> untuk mengirim email sungguhan.</p>
            <a href="{{ $devUrl }}" class="mt-2 inline-block font-semibold underline">Verifikasi sekarang (khusus lokal)</a>
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="h-11 rounded-full bg-brand px-6 text-sm font-semibold text-white transition hover:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2">Kirim Ulang Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-slate-500 underline hover:text-slate-800">Keluar</button>
        </form>
    </div>
</x-auth-layout>
