<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $code }}</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; font-family: 'DejaVu Sans', sans-serif; color: #1e293b; }
        /* A4 landscape = 842 x 595 pt. Ukuran eksplisit (dompdf kurang akurat pada right/bottom bersarang). */
        .frame { position: absolute; top: 20pt; left: 20pt; width: 794pt; height: 547pt; border: 4pt solid #3730a3; }
        .inner { position: absolute; top: 7pt; left: 7pt; width: 778pt; height: 531pt; border: 1pt solid #a5b4fc; }
        .content { position: absolute; top: 78pt; left: 70pt; right: 70pt; text-align: center; }
        .brand { font-size: 13pt; font-weight: bold; letter-spacing: 4pt; color: #3730a3; text-transform: uppercase; }
        .title { margin-top: 14pt; font-size: 30pt; font-weight: bold; color: #0f172a; letter-spacing: 2pt; }
        .lead { margin-top: 22pt; font-size: 11pt; color: #64748b; }
        .name { margin-top: 10pt; font-family: 'DejaVu Serif', serif; font-weight: bold; color: #3730a3; }
        .rule { width: 260pt; margin: 10pt auto 0; border-top: 1pt solid #cbd5e1; }
        .course { margin-top: 12pt; font-size: 19pt; font-weight: bold; color: #0f172a; }
        .meta { margin-top: 8pt; font-size: 10pt; color: #475569; }
        .footer { position: absolute; left: 70pt; top: 468pt; width: 702pt; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { vertical-align: bottom; font-size: 9pt; color: #475569; }
        .sign-line { width: 170pt; border-top: 1pt solid #64748b; padding-top: 4pt; }
        .code { font-size: 10pt; font-weight: bold; color: #0f172a; letter-spacing: 1pt; }
        .verify { font-size: 8pt; color: #64748b; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="frame"><div class="inner"></div></div>

    <div class="content">
        <div class="brand">Crygle Academy</div>
        <div class="title">SERTIFIKAT PENYELESAIAN</div>

        <div class="lead">Diberikan kepada</div>
        <div class="name" style="font-size: {{ mb_strlen($studentName) > 32 ? '24pt' : '34pt' }};">{{ $studentName }}</div>
        <div class="rule"></div>

        <div class="lead" style="margin-top: 14pt;">atas keberhasilannya menyelesaikan seluruh materi dan tugas pada kelas</div>
        <div class="course">{{ $courseTitle }}</div>
        <div class="meta">Tingkat {{ $courseLevel }} &middot; {{ $lessonCount }} materi &middot; Diselesaikan pada {{ $completedOn }}</div>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td style="width: 45%;">
                    <div class="sign-line"><strong>{{ $mentorName }}</strong><br>Mentor Kelas</div>
                </td>
                <td style="width: 55%; text-align: right;">
                    <div class="code">{{ $code }}</div>
                    <div class="verify">Verifikasi keaslian: {{ $verifyUrl }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
