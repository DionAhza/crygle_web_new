<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function show(Request $request, Enrollment $enrollment, MidtransService $midtrans): View|RedirectResponse
    {
        $this->ensureOwner($request, $enrollment);

        if ($enrollment->isPaid()) {
            return redirect()->route('student.courses');
        }

        $enrollment->load('course.mentor:id,name');

        $snapToken = null;
        $error = null;
        if ($midtrans->configured()) {
            try {
                $snapToken = $midtrans->createSnapToken($enrollment);
            } catch (Throwable $e) {
                report($e);
                $error = 'Gagal menghubungi layanan pembayaran. Silakan coba lagi beberapa saat.';
            }
        }

        return view('student.checkout', [
            'enrollment' => $enrollment,
            'snapToken' => $snapToken,
            'snapJsUrl' => $midtrans->snapJsUrl(),
            'clientKey' => $midtrans->clientKey(),
            'canSimulate' => app()->environment('local') && ! $midtrans->configured(),
            'error' => $error,
        ]);
    }

    /** Hanya untuk pengembangan lokal tanpa kunci Midtrans. */
    public function simulate(Request $request, Enrollment $enrollment, MidtransService $midtrans): RedirectResponse
    {
        $this->ensureOwner($request, $enrollment);
        abort_unless(app()->environment('local') && ! $midtrans->configured(), 404);

        $enrollment->update(['payment_status' => 'paid']);

        return redirect()->route('student.courses')->with('status', 'Pembayaran disimulasikan. Selamat belajar!');
    }

    private function ensureOwner(Request $request, Enrollment $enrollment): void
    {
        abort_unless($enrollment->student_id === $request->user()->id, 403);
    }
}
