<x-auth-layout title="Masuk">
    <h1 class="mt-10 text-2xl font-bold tracking-tight">Halo, Selamat Datang 👋</h1>
    <p class="mt-1 text-xs text-slate-400">Silahkan Login dan Masukin Akun Kamu</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <x-auth-field name="email" label="Alamat Email" type="email" required autofocus autocomplete="username" />
        <x-auth-field name="password" label="Password" type="password" required autocomplete="current-password" />

        <div class="flex items-center justify-between text-xs">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand">
                <span>Ingatkan Saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="font-medium text-[#202020] hover:text-brand hover:underline">Lupa Password?</a>
            @endif
        </div>

        <button type="submit" class="h-11 w-full rounded-full bg-brand text-sm font-semibold text-white transition hover:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2">Masuk</button>
    </form>

    <x-auth-social divider="Atau login dengan" label="Lanjutkan dengan Google" />

    <p class="mt-6 text-xs">Apakah kamu belum memiliki akun? Silahkan <a href="{{ route('register') }}" class="font-bold text-[#3069a2] hover:underline">Buat Akun</a></p>
</x-auth-layout>
