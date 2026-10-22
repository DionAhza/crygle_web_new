<x-auth-layout title="Buat Akun">
    <h1 class="mt-10 text-2xl font-bold tracking-tight">Silahkan Membuat Akun</h1>
    <p class="mt-1 text-xs text-slate-400">Please masukin detail disini</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <x-auth-field name="name" label="Full Name" required autofocus autocomplete="name" />
        <x-auth-field name="email" label="Email Address" type="email" required autocomplete="username" />
        <x-auth-field name="password" label="Password" type="password" required autocomplete="new-password" />
        <x-auth-field name="password_confirmation" label="Konfirmasi Password" type="password" required autocomplete="new-password" />

        <div>
            <label for="terms" class="inline-flex items-center gap-2 text-xs">
                <input id="terms" name="terms" type="checkbox" value="1" required @checked(old('terms')) class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand">
                <span>I agree to the Terms &amp; Conditions</span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-1.5" />
        </div>

        <button type="submit" class="h-11 w-full rounded-full bg-brand text-sm font-semibold text-white transition hover:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2">Buat Akun</button>
    </form>

    <x-auth-social divider="Or login with" label="Continue with Google" />

    <p class="mt-6 text-xs">Apakah kamu sudah memiliki akun? Silahkan <a href="{{ route('login') }}" class="font-bold text-[#3069a2] hover:underline">Masuk</a></p>
</x-auth-layout>
