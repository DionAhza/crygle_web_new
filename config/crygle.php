<?php

return [
    'default_passing_score' => (int) env('CRYGLE_DEFAULT_PASSING_SCORE', 75),
    'certificate_prefix' => env('CRYGLE_CERTIFICATE_PREFIX', 'CRYGLE-CERT-'),
    'video_storage_driver' => env('VIDEO_STORAGE_DRIVER', 'youtube'),
];
