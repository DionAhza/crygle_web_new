<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\CourseProgressService;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function show(Request $request, Enrollment $enrollment, MidtransService $midtrans): View|RedirectResponse
    {
        $this->ensureOwner($request, $enrollment);

        // Bila ternyata sudah dibayar (webhook tidak sampai), aktifkan sekarang.
        if ($midtrans->syncEnrollment($enrollment) || $enrollment->isPaid()) {
            return redirect()->route('student.checkout.success', $enrollment);
        }

        $enrollment->load(['course.mentor:id,name', 'student:id,name']);

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

    /** Dipanggil setelah Snap selesai: cek status ke Midtrans, lalu tampilkan "memproses" atau hasil. */
    public function finish(Request $request, Enrollment $enrollment, MidtransService $midtrans): View|RedirectResponse
    {
        $this->ensureOwner($request, $enrollment);

        $orderId = $request->query('order_id');
        $orderId = is_string($orderId) ? $orderId : null;

        if ($midtrans->syncEnrollment($enrollment, $orderId) || $enrollment->isPaid()) {
            return redirect()->route('student.checkout.success', $enrollment);
        }

        $enrollment->load('course');

        return view('student.checkout-processing', [
            'enrollment' => $enrollment,
            'orderId' => $this->displayOrderId($enrollment, $orderId),
        ]);
    }

    /** Polling dari halaman "memproses": sinkronkan status lalu kabarkan hasilnya. */
    public function status(Request $request, Enrollment $enrollment, MidtransService $midtrans): JsonResponse
    {
        $this->ensureOwner($request, $enrollment);

        $midtrans->syncEnrollment($enrollment);
        $enrollment->refresh();

        return response()->json(['paid' => $enrollment->isPaid(), 'status' => $enrollment->payment_status]);
    }

    public function success(Request $request, Enrollment $enrollment, CourseProgressService $progress): View|RedirectResponse
    {
        $this->ensureOwner($request, $enrollment);

        if (! $enrollment->isPaid()) {
            return redirect()->route('student.checkout', $enrollment);
        }

        $enrollment->load('course.mentor:id,name');
        $first = $progress->orderedLessons($enrollment->course)->first();

        return view('student.checkout-success', [
            'enrollment' => $enrollment,
            'course' => $enrollment->course,
            'startUrl' => $first ? route('learn.show', [$enrollment->course, $first]) : route('student.courses'),
        ]);
    }

    public function receipt(Request $request, Enrollment $enrollment): View|RedirectResponse
    {
        $this->ensureOwner($request, $enrollment);

        if (! $enrollment->isPaid()) {
            return redirect()->route('student.checkout', $enrollment);
        }

        $enrollment->load(['course.mentor:id,name', 'student:id,name']);

        return view('student.checkout-receipt', [
            'enrollment' => $enrollment,
            'orderId' => $this->displayOrderId($enrollment, null),
            'paidOn' => $enrollment->updated_at->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i').' WIB',
        ]);
    }

    private function displayOrderId(Enrollment $enrollment, ?string $given): string
    {
        $id = $given ?: $enrollment->midtrans_order_id;

        return $id ?: 'ENR-'.$enrollment->id;
    }

    /** Hanya untuk pengembangan lokal tanpa kunci Midtrans. */
    public function simulate(Request $request, Enrollment $enrollment, MidtransService $midtrans): RedirectResponse
    {
        $this->ensureOwner($request, $enrollment);
        abort_unless(app()->environment('local') && ! $midtrans->configured(), 404);

        $enrollment->update(['payment_status' => 'paid']);

        return redirect()->route('student.checkout.success', $enrollment);
    }

    private function ensureOwner(Request $request, Enrollment $enrollment): void
    {
        abort_unless($enrollment->student_id === $request->user()->id, 403);
    }
}
