<?php

return [
    'default_passing_score' => (int) env('CRYGLE_DEFAULT_PASSING_SCORE', 75),
    'certificate_prefix' => env('CRYGLE_CERTIFICATE_PREFIX', 'CRYGLE-CERT-'),
    'video_storage_driver' => env('VIDEO_STORAGE_DRIVER', 'youtube'),

    // Disk penyimpanan berkas tugas siswa (private). Gunakan "s3" di produksi.
    'submission_disk' => env('SUBMISSION_DISK', 'local'),
    // Batas ukuran berkas tugas (KB). Default 10 MB.
    'submission_max_kb' => (int) env('SUBMISSION_MAX_KB', 10240),
];
