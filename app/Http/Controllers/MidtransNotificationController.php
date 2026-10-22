<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->validSignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $id = $midtrans->enrollmentIdFromOrderId((string) ($payload['order_id'] ?? ''));
        $enrollment = $id ? Enrollment::with('course')->find($id) : null;

        if (! $enrollment) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Pastikan nominal sesuai harga kelas (mencegah pembayaran kurang).
        if ((int) round((float) ($payload['gross_amount'] ?? 0)) !== $midtrans->grossAmount($enrollment)) {
            return response()->json(['message' => 'Amount mismatch'], 422);
        }

        // Pendaftaran yang sudah lunas tidak boleh diturunkan statusnya.
        if (! $enrollment->isPaid()) {
            $enrollment->update(['payment_status' => $midtrans->mapStatus($payload)]);
        }

        return response()->json(['message' => 'OK']);
    }
}
