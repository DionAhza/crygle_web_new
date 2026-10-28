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

    /** 765 => "12:45", 3725 => "1:02:05" (format pemutar video) */
    public static function clock(int $seconds): string
    {
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        return $h > 0 ? sprintf('%d:%02d:%02d', $h, $m, $s) : sprintf('%d:%02d', $m, $s);
    }

    public static function rupiah(float|int|string $amount): string
    {
        return (float) $amount <= 0 ? 'Gratis' : 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}
