<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Throwable;

class GoogleController extends Controller
{
    /** Mengarahkan pengguna ke halaman persetujuan Google. */
    public function redirect(): RedirectResponse
    {
        if (! $this->configured()) {
            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google belum dikonfigurasi.']);
        }

        return Socialite::driver('google')->redirect();
    }

    /** Menerima hasil login dari Google, lalu menautkan atau membuat akun. */
    public function callback(): RedirectResponse
    {
        if (! $this->configured()) {
            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google belum dikonfigurasi.']);
        }

        try {
            $google = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google dibatalkan atau gagal. Silakan coba lagi.']);
        }

        $email = Str::lower(trim((string) $google->getEmail()));

        // Hanya percaya alamat email yang sudah diverifikasi oleh Google
        // (mencegah pengambilalihan akun lewat email yang belum terverifikasi).
        if ($email === '' || ! filter_var($google->user['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('login')->withErrors(['email' => 'Email akun Google Anda belum terverifikasi.']);
        }

        $user = User::where('google_id', $google->getId())->first()
            ?? User::where('email', $email)->first();

        if ($user) {
            // Akun sudah ada (manual/dibuat Admin): tautkan Google, role tidak diubah.
            $user->forceFill([
                'google_id' => $user->google_id ?: $google->getId(),
                'avatar_url' => $user->avatar_url ?: $google->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        } else {
            $user = User::create([
                'name' => $google->getName() ?: Str::before($email, '@'),
                'email' => $email,
                // Pengguna Google tidak memakai password; isi acak agar kolom tetap terisi.
                // Mereka dapat membuat password lewat fitur "Lupa Password".
                'password' => Str::random(40),
                'avatar_url' => $google->getAvatar(),
            ]);
            $user->forceFill(['google_id' => $google->getId(), 'email_verified_at' => now()])->save();
            $user->assignRole(Role::findOrCreate('student', 'web'));
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function configured(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }
}
