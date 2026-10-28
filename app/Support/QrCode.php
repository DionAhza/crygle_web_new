<?php

namespace App\Support;

/**
 * Pembuat QR Code sederhana (mode byte, koreksi error M, versi 1–10 → hingga 213 byte).
 * Tanpa dependensi; cukup untuk URL verifikasi sertifikat. Keluaran PNG (butuh ekstensi GD).
 */
class QrCode
{
    /** versi => [ecc per blok, [[jumlah blok, data codeword per blok], ...]] untuk level M */
    private const BLOCKS = [
        1 => [10, [[1, 16]]], 2 => [16, [[1, 28]]], 3 => [26, [[1, 44]]], 4 => [18, [[2, 32]]],
        5 => [24, [[2, 43]]], 6 => [16, [[4, 27]]], 7 => [18, [[4, 31]]], 8 => [22, [[2, 38], [2, 39]]],
        9 => [22, [[3, 36], [2, 37]]], 10 => [26, [[4, 43], [1, 44]]],
    ];

    private const ALIGN = [
        1 => [], 2 => [6, 18], 3 => [6, 22], 4 => [6, 26], 5 => [6, 30], 6 => [6, 34],
        7 => [6, 22, 38], 8 => [6, 24, 42], 9 => [6, 26, 46], 10 => [6, 28, 50],
    ];

    private array $exp = [];

    private array $log = [];

    private int $size;

    /** @var array<int, array<int, bool>> */
    private array $modules = [];

    /** @var array<int, array<int, bool>> */
    private array $isFunction = [];

    private function __construct()
    {
        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            $this->exp[$i] = $x;
            $this->log[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) {
                $x ^= 0x11D;
            }
        }
    }

    /** @return array<int, array<int, bool>> matriks modul (true = gelap) */
    public static function matrix(string $text): array
    {
        $qr = new self;

        return $qr->build($text);
    }

    /** PNG biner; $scale = piksel per modul, $quiet = modul zona tenang. */
    public static function png(string $text, int $scale = 8, int $quiet = 2): string
    {
        $m = self::matrix($text);
        $n = count($m);
        $dim = ($n + 2 * $quiet) * $scale;
        $img = imagecreate($dim, $dim);
        $white = imagecolorallocate($img, 255, 255, 255);
        $dark = imagecolorallocate($img, 17, 24, 39);
        imagefill($img, 0, 0, $white);
        foreach ($m as $y => $row) {
            foreach ($row as $x => $on) {
                if ($on) {
                    imagefilledrectangle($img, ($x + $quiet) * $scale, ($y + $quiet) * $scale,
                        ($x + $quiet + 1) * $scale - 1, ($y + $quiet + 1) * $scale - 1, $dark);
                }
            }
        }
        ob_start();
        imagepng($img);
        $png = (string) ob_get_clean();
        imagedestroy($img);

        return $png;
    }

    public static function pngDataUri(string $text, int $scale = 8, int $quiet = 2): string
    {
        return 'data:image/png;base64,'.base64_encode(self::png($text, $scale, $quiet));
    }

    private function build(string $text): array
    {
        $data = array_values(unpack('C*', $text) ?: []);

        $version = null;
        foreach (self::BLOCKS as $v => [$ecc, $groups]) {
            $capacity = array_sum(array_map(fn ($g) => $g[0] * $g[1], $groups));
            $countBits = $v >= 10 ? 16 : 8;
            if ((4 + $countBits + 8 * count($data) + 3) >> 3 <= $capacity) { // +3: pembulatan ke byte
                $version = $v;
                break;
            }
        }
        if ($version === null) {
            throw new \InvalidArgumentException('Teks terlalu panjang untuk QR (maks. 213 byte).');
        }

        $codewords = $this->interleave($this->encodeData($data, $version), $version);
        $this->size = $version * 4 + 17;

        $best = null;
        $bestPenalty = PHP_INT_MAX;
        for ($mask = 0; $mask < 8; $mask++) {
            $this->drawFunctionPatterns($version);
            $this->drawCodewords($codewords);
            $this->applyMask($mask);
            $this->drawFormatBits($mask);
            $penalty = $this->penalty();
            if ($penalty < $bestPenalty) {
                $bestPenalty = $penalty;
                $best = $this->modules;
            }
        }

        return $best;
    }

    /** @return int[] data codeword (sudah dipadding) */
    private function encodeData(array $data, int $version): array
    {
        $capacity = array_sum(array_map(fn ($g) => $g[0] * $g[1], self::BLOCKS[$version][1]));
        $bits = '0100'.str_pad(decbin(count($data)), $version >= 10 ? 16 : 8, '0', STR_PAD_LEFT);
        foreach ($data as $b) {
            $bits .= str_pad(decbin($b), 8, '0', STR_PAD_LEFT);
        }
        $bits .= str_repeat('0', min(4, $capacity * 8 - strlen($bits)));
        $bits .= str_repeat('0', (8 - strlen($bits) % 8) % 8);

        $cw = array_map('bindec', str_split($bits, 8));
        for ($pad = 0xEC; count($cw) < $capacity; $pad ^= 0xEC ^ 0x11) {
            $cw[] = $pad;
        }

        return $cw;
    }

    private function interleave(array $cw, int $version): array
    {
        [$eccLen, $groups] = self::BLOCKS[$version];
        $gen = $this->generator($eccLen);

        $dataBlocks = [];
        $pos = 0;
        foreach ($groups as [$count, $len]) {
            for ($i = 0; $i < $count; $i++) {
                $dataBlocks[] = array_slice($cw, $pos, $len);
                $pos += $len;
            }
        }
        $eccBlocks = array_map(fn ($b) => $this->remainder($b, $gen), $dataBlocks);

        $out = [];
        $max = max(array_map('count', $dataBlocks));
        for ($i = 0; $i < $max; $i++) {
            foreach ($dataBlocks as $b) {
                if (isset($b[$i])) {
                    $out[] = $b[$i];
                }
            }
        }
        for ($i = 0; $i < $eccLen; $i++) {
            foreach ($eccBlocks as $b) {
                $out[] = $b[$i];
            }
        }

        return $out;
    }

    private function mul(int $a, int $b): int
    {
        return ($a === 0 || $b === 0) ? 0 : $this->exp[($this->log[$a] + $this->log[$b]) % 255];
    }

    /** @return int[] koefisien generator (tanpa suku pangkat tertinggi) */
    private function generator(int $degree): array
    {
        $poly = array_fill(0, $degree, 0);
        $poly[$degree - 1] = 1;
        $root = 1;
        for ($i = 0; $i < $degree; $i++) {
            for ($j = 0; $j < $degree; $j++) {
                $poly[$j] = $this->mul($poly[$j], $root);
                if ($j + 1 < $degree) {
                    $poly[$j] ^= $poly[$j + 1];
                }
            }
            $root = $this->mul($root, 2);
        }

        return $poly;
    }

    private function remainder(array $data, array $gen): array
    {
        $rem = array_fill(0, count($gen), 0);
        foreach ($data as $b) {
            $factor = $b ^ array_shift($rem);
            $rem[] = 0;
            foreach ($gen as $i => $coef) {
                $rem[$i] ^= $this->mul($coef, $factor);
            }
        }

        return $rem;
    }

    private function set(int $x, int $y, bool $dark): void
    {
        $this->modules[$y][$x] = $dark;
        $this->isFunction[$y][$x] = true;
    }

    private function drawFunctionPatterns(int $version): void
    {
        $n = $this->size;
        $this->modules = array_fill(0, $n, array_fill(0, $n, false));
        $this->isFunction = array_fill(0, $n, array_fill(0, $n, false));

        for ($i = 0; $i < $n; $i++) {
            $this->set(6, $i, $i % 2 === 0);
            $this->set($i, 6, $i % 2 === 0);
        }
        foreach ([[3, 3], [$n - 4, 3], [3, $n - 4]] as [$cx, $cy]) {
            for ($dy = -4; $dy <= 4; $dy++) {
                for ($dx = -4; $dx <= 4; $dx++) {
                    $x = $cx + $dx;
                    $y = $cy + $dy;
                    if ($x >= 0 && $x < $n && $y >= 0 && $y < $n) {
                        $d = max(abs($dx), abs($dy));
                        $this->set($x, $y, $d !== 2 && $d !== 4);
                    }
                }
            }
        }
        $pos = self::ALIGN[$version];
        $last = count($pos) - 1;
        foreach ($pos as $i => $cx) {
            foreach ($pos as $j => $cy) {
                if (($i === 0 && $j === 0) || ($i === 0 && $j === $last) || ($i === $last && $j === 0)) {
                    continue;
                }
                for ($dy = -2; $dy <= 2; $dy++) {
                    for ($dx = -2; $dx <= 2; $dx++) {
                        $this->set($cx + $dx, $cy + $dy, max(abs($dx), abs($dy)) !== 1);
                    }
                }
            }
        }
        $this->drawFormatBits(0);
        if ($version >= 7) {
            $rem = $version;
            for ($i = 0; $i < 12; $i++) {
                $rem = ($rem << 1) ^ (($rem >> 11) * 0x1F25);
            }
            $bits = ($version << 12) | $rem;
            for ($i = 0; $i < 18; $i++) {
                $bit = (($bits >> $i) & 1) === 1;
                $a = $n - 11 + $i % 3;
                $b = intdiv($i, 3);
                $this->set($a, $b, $bit);
                $this->set($b, $a, $bit);
            }
        }
    }

    private function drawFormatBits(int $mask): void
    {
        $n = $this->size;
        $data = (0 << 3) | $mask; // level M = 00
        $rem = $data;
        for ($i = 0; $i < 10; $i++) {
            $rem = ($rem << 1) ^ (($rem >> 9) * 0x537);
        }
        $bits = (($data << 10) | $rem) ^ 0x5412;
        $bit = fn (int $i) => (($bits >> $i) & 1) === 1;

        for ($i = 0; $i <= 5; $i++) {
            $this->set(8, $i, $bit($i));
        }
        $this->set(8, 7, $bit(6));
        $this->set(8, 8, $bit(7));
        $this->set(7, 8, $bit(8));
        for ($i = 9; $i < 15; $i++) {
            $this->set(14 - $i, 8, $bit($i));
        }
        for ($i = 0; $i < 8; $i++) {
            $this->set($n - 1 - $i, 8, $bit($i));
        }
        for ($i = 8; $i < 15; $i++) {
            $this->set(8, $n - 15 + $i, $bit($i));
        }
        $this->set(8, $n - 8, true);
    }

    private function drawCodewords(array $codewords): void
    {
        $n = $this->size;
        $total = count($codewords) * 8;
        $i = 0;
        for ($right = $n - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right = 5;
            }
            for ($vert = 0; $vert < $n; $vert++) {
                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;
                    $upward = (($right + 1) & 2) === 0;
                    $y = $upward ? $n - 1 - $vert : $vert;
                    if (! $this->isFunction[$y][$x] && $i < $total) {
                        $this->modules[$y][$x] = ((($codewords[$i >> 3] >> (7 - ($i & 7))) & 1) === 1);
                        $i++;
                    }
                }
            }
        }
    }

    private function applyMask(int $mask): void
    {
        for ($y = 0; $y < $this->size; $y++) {
            for ($x = 0; $x < $this->size; $x++) {
                $invert = match ($mask) {
                    0 => ($x + $y) % 2 === 0,
                    1 => $y % 2 === 0,
                    2 => $x % 3 === 0,
                    3 => ($x + $y) % 3 === 0,
                    4 => (intdiv($x, 3) + intdiv($y, 2)) % 2 === 0,
                    5 => $x * $y % 2 + $x * $y % 3 === 0,
                    6 => ($x * $y % 2 + $x * $y % 3) % 2 === 0,
                    default => (($x + $y) % 2 + $x * $y % 3) % 2 === 0,
                };
                if ($invert && ! $this->isFunction[$y][$x]) {
                    $this->modules[$y][$x] = ! $this->modules[$y][$x];
                }
            }
        }
    }

    private function penalty(): int
    {
        $n = $this->size;
        $m = $this->modules;
        $score = 0;

        // Aturan 1 & 3 pada baris lalu kolom.
        foreach ([false, true] as $transpose) {
            for ($a = 0; $a < $n; $a++) {
                $line = [];
                for ($b = 0; $b < $n; $b++) {
                    $line[] = $transpose ? $m[$b][$a] : $m[$a][$b];
                }
                $run = 1;
                for ($b = 1; $b <= $n; $b++) {
                    if ($b < $n && $line[$b] === $line[$b - 1]) {
                        $run++;
                    } else {
                        if ($run >= 5) {
                            $score += 3 + ($run - 5);
                        }
                        $run = 1;
                    }
                }
                $str = implode('', array_map(fn ($v) => $v ? '1' : '0', $line));
                $score += 40 * (substr_count($str, '10111010000') + substr_count($str, '00001011101'));
            }
        }
        // Aturan 2: blok 2x2 sewarna.
        for ($y = 0; $y < $n - 1; $y++) {
            for ($x = 0; $x < $n - 1; $x++) {
                if ($m[$y][$x] === $m[$y][$x + 1] && $m[$y][$x] === $m[$y + 1][$x] && $m[$y][$x] === $m[$y + 1][$x + 1]) {
                    $score += 3;
                }
            }
        }
        // Aturan 4: keseimbangan gelap/terang.
        $dark = 0;
        foreach ($m as $row) {
            $dark += count(array_filter($row));
        }
        $k = (int) ceil(abs($dark * 20 - $n * $n * 10) / ($n * $n)) - 1;

        return $score + max(0, $k) * 10;
    }
}
