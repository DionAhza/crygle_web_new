<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Mode pengembangan: jika email hanya ditulis ke log (MAIL_MAILER=log), tampilkan
        // tautan verifikasi langsung agar bisa dicoba tanpa membuka file log.
        $devUrl = (app()->environment('local') && config('mail.default') === 'log')
            ? URL::temporarySignedRoute('verification.verify', now()->addMinutes((int) config('auth.verification.expire', 60)),
                ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())])
            : null;

        return view('auth.verify-email', ['devUrl' => $devUrl, 'mailer' => config('mail.default')]);
    }
}
