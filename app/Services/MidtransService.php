<?php

namespace App\Services;

use App\Models\Enrollment;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function configured(): bool
    {
        return filled(config('services.midtrans.server_key')) && filled(config('services.midtrans.client_key'));
    }

    public function clientKey(): ?string
    {
        return config('services.midtrans.client_key');
    }

    public function snapJsUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /** Order ID unik per percobaan bayar; id enrollment bisa dibaca kembali dari prefiks. */
    public function orderId(Enrollment $enrollment): string
    {
        return 'ENR-'.$enrollment->id.'-'.now()->timestamp;
    }

    public function enrollmentIdFromOrderId(string $orderId): ?int
    {
        return preg_match('/^ENR-(\d+)-\d+$/', $orderId, $m) ? (int) $m[1] : null;
    }

    public function grossAmount(Enrollment $enrollment): int
    {
        return (int) round((float) $enrollment->course->price);
    }

    /** Membuat Snap token untuk enrollment (memanggil API Midtrans). */
    public function createSnapToken(Enrollment $enrollment): string
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $enrollment->loadMissing(['course', 'student']);

        return Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $this->orderId($enrollment),
                'gross_amount' => $this->grossAmount($enrollment),
            ],
            'item_details' => [[
                'id' => 'course-'.$enrollment->course_id,
                'price' => $this->grossAmount($enrollment),
                'quantity' => 1,
                'name' => mb_substr($enrollment->course->title, 0, 50),
            ]],
            'customer_details' => [
                'first_name' => $enrollment->student->name,
                'email' => $enrollment->student->email,
            ],
        ]);
    }

    /** signature_key = SHA512(order_id + status_code + gross_amount + server_key) */
    public function validSignature(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');
        if ($serverKey === '' || empty($payload['signature_key'])) {
            return false;
        }

        $expected = hash('sha512',
            ($payload['order_id'] ?? '').($payload['status_code'] ?? '').($payload['gross_amount'] ?? '').$serverKey);

        return hash_equals($expected, (string) $payload['signature_key']);
    }

    /** Memetakan status transaksi Midtrans ke payment_status enrollment. */
    public function mapStatus(array $payload): string
    {
        return match ($payload['transaction_status'] ?? '') {
            'settlement' => 'paid',
            'capture' => ($payload['fraud_status'] ?? 'accept') === 'accept' ? 'paid' : 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            default => 'pending',
        };
    }
}
