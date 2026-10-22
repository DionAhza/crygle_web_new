<?php

namespace App\Support;

class Format
{
    /** 3900 => "1 jam 5 menit", 600 => "10 menit" */
    public static function duration(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($hours > 0) {
            return $hours.' jam'.($minutes ? ' '.$minutes.' menit' : '');
        }

        return max($minutes, $seconds > 0 ? 1 : 0).' menit';
    }

    public static function rupiah(float|int|string $amount): string
    {
        return (float) $amount <= 0 ? 'Gratis' : 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}
