@php
    // Template: 2 halaman A4 landscape (842 x 595 pt). Latar = desain Crygle Academy (JPEG);
    // hanya teks dinamis yang digambar di atasnya. Koordinat dalam pt.
    $font = fn (int $w) => 'file://'.str_replace('\\', '/', resource_path("fonts/PlusJakartaSans-{$w}.ttf"));
    $bgCert = 'file://'.str_replace('\\', '/', resource_path('certificates/cert_bg.jpg'));
    $bgScore = 'file://'.str_replace('\\', '/', resource_path('certificates/score_bg.jpg'));

    $nameLen = mb_strlen($studentName);
    $nameSize = $nameLen > 34 ? 18 : ($nameLen > 26 ? 23 : ($nameLen > 20 ? 28 : 33));
    $courseLen = mb_strlen($courseTitle);
    $courseSize = $courseLen > 70 ? 11 : ($courseLen > 48 ? 13 : 16);

    $rows = $assessments->take(12);
    $bodyHeight = 262;
    $rowHeight = $rows->isEmpty() ? $bodyHeight : $bodyHeight / $rows->count();
    $cellSize = $rows->count() > 8 ? 9 : 11.5;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $code }}</title>
    <style>
        @font-face { font-family: 'Jakarta'; font-weight: 400; src: url('{{ $font(400) }}') format('truetype'); }
        @font-face { font-family: 'Jakarta'; font-weight: 500; src: url('{{ $font(500) }}') format('truetype'); }
        @font-face { font-family: 'Jakarta'; font-weight: 600; src: url('{{ $font(600) }}') format('truetype'); }
        @font-face { font-family: 'Jakarta'; font-weight: 700; src: url('{{ $font(700) }}') format('truetype'); }
        @font-face { font-family: 'Jakarta'; font-weight: 800; src: url('{{ $font(800) }}') format('truetype'); }
        @page { margin: 0; size: 842pt 595pt; }
        body { margin: 0; font-family: 'Jakarta', 'DejaVu Sans', sans-serif; color: #111827; }
        .page { position: relative; width: 842pt; height: 595pt; overflow: hidden; }
        .bg { position: absolute; top: 0; left: 0; width: 842pt; height: 595pt; }
        .abs { position: absolute; }
        .center { left: 0; width: 842pt; text-align: center; }

        .name { top: 222pt; line-height: 44pt; font-weight: 800; color: #1a2e68; }
        .course { left: 121pt; width: 600pt; top: 323pt; line-height: 20pt; font-weight: 700; color: #111827; }
        .quote { top: 356pt; line-height: 15.8pt; font-size: 13pt; font-weight: 500; color: #111827; }
        .qr { left: 727pt; top: 479pt; width: 52pt; height: 52pt; }
        .qr-cap { left: 717pt; width: 72pt; top: 533pt; text-align: center; font-size: 5.8pt; line-height: 7pt; color: #334155; }
        .code { left: 72pt; top: 517pt; font-size: 7.5pt; line-height: 11pt; color: #334155; }
        .code b { font-weight: 700; }

        .lbl { left: 72pt; font-size: 13pt; font-weight: 700; line-height: 18pt; }
        .col { left: 188pt; font-size: 13pt; line-height: 18pt; }
        .val { left: 203pt; width: 330pt; font-size: 13pt; line-height: 18pt; font-weight: 500; }

        .tbl-wrap { position: absolute; left: 70.7pt; top: 152.8pt; width: 699.7pt; height: 363.7pt; border: 1.3pt solid #111; border-radius: 9pt; background: #fff; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { padding: 0 9pt; font-size: {{ $cellSize }}pt; line-height: {{ $cellSize + 3.2 }}pt; vertical-align: middle; border: 1.3pt solid #111; font-weight: 500; }
        th { font-weight: 500; color: #111; background: #aed5fc; height: 31.6pt; font-size: 13pt; text-align: center; padding: 0; }
        .bar { background: #235f9c; color: #fff; text-align: center; font-size: 13pt; height: 31.6pt; padding: 0; border-top: 0; border-left: 0; border-right: 0; font-weight: 500; }
        .foot td { background: #aed5fc; height: 31.2pt; font-size: 11pt; text-align: center; border-bottom: 0; padding: 0; }
        td.score { text-align: center; font-size: {{ $cellSize + 2.5 }}pt; padding: 0; }
        td.first, th.first, .bar { border-left: 0; }
        td.last, th.last { border-right: 0; }
    </style>
</head>
<body>
    {{-- Halaman 1: sertifikat --}}
    <div class="page">
        <img class="bg" src="{{ $bgCert }}">
        <div class="abs center name" style="font-size: {{ $nameSize }}pt;">{{ $studentName }}</div>
        <div class="abs course" style="font-size: {{ $courseSize }}pt; text-align: center;">{{ $courseTitle }}</div>
        <div class="abs center quote">This certificate is awarded in recognition of successfully completing the program and<br>demonstrating mastery of the course material at Crygle Academy.</div>
        @if ($qr)
            <img class="abs qr" src="{{ $qr }}">
            <div class="abs qr-cap">Scan untuk verifikasi</div>
        @endif
        <div class="abs code">No. <b>{{ $code }}</b> &nbsp;&middot;&nbsp; Diselesaikan {{ $completedOn }}<br>Verifikasi keaslian: {{ $verifyUrl }}</div>
    </div>

    {{-- Halaman 2: hasil penilaian (hanya bila kelas memiliki tugas yang sudah dinilai) --}}
    @if ($rows->isNotEmpty())
        <div class="page" style="page-break-before: always;">
            <img class="bg" src="{{ $bgScore }}">

            <div class="abs lbl" style="top: 78pt;">Nama Lengkap</div><div class="abs col" style="top: 78pt;">:</div><div class="abs val" style="top: 78pt;">{{ $studentName }}</div>
            <div class="abs lbl" style="top: 98.5pt;">Course</div><div class="abs col" style="top: 98.5pt;">:</div><div class="abs val" style="top: 98.5pt;">{{ \Illuminate\Support\Str::limit($courseTitle, 48) }}</div>
            <div class="abs lbl" style="top: 119pt;">Class level</div><div class="abs col" style="top: 119pt;">:</div><div class="abs val" style="top: 119pt;">{{ $courseLevel }}</div>

            <div class="tbl-wrap">
                <table>
                    <colgroup><col style="width: 32.9%"><col style="width: 52.7%"><col style="width: 14.4%"></colgroup>
                    <tr><td colspan="3" class="bar">Assessment Results</td></tr>
                    <tr><th class="first">Competence</th><th>Detailed Competencies</th><th class="last">Scores</th></tr>
                    @foreach ($rows as $row)
                        <tr>
                            <td class="first" style="height: {{ round($rowHeight, 2) }}pt;">{{ \Illuminate\Support\Str::limit($row['title'], 60) }}</td>
                            <td>{{ $row['detail'] }}</td>
                            <td class="score last">{{ $row['score'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="foot"><td colspan="2" class="first">Total Score</td><td class="score last" style="font-weight: 700;">{{ $totalScore }}</td></tr>
                </table>
            </div>
        </div>
    @endif
</body>
</html>
