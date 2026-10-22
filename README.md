# Crygle Academy

Platform kursus online (LMS) berbasis evaluasi ketat, mirip Dicoding. Dibangun dengan Laravel 13, Blade, Alpine.js, dan Tailwind CSS.

## Fitur
- **Katalog & pendaftaran kelas** dengan pembayaran Midtrans (mode simulasi tersedia untuk lokal).
- **Dashboard mentor**: kelas, modul, materi video, tugas, dan antrean review.
- **Ruang belajar berurutan**: materi berikutnya terkunci sampai materi sebelumnya selesai.
- **Tugas & review**: pengiriman file / link repo / teks, riwayat revisi tidak pernah ditimpa.
- **Sertifikat PDF otomatis** saat progres 100%, dengan halaman verifikasi publik.

## Menjalankan secara lokal
```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm ci && npm run build   # atau `npm run dev`
php artisan serve
```

## Pengujian
```bash
npm run build        # Vite manifest diperlukan oleh test yang merender halaman
php artisan test
```

## Variabel lingkungan penting
`CRYGLE_DEFAULT_PASSING_SCORE`, `CRYGLE_CERTIFICATE_PREFIX`, `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`, `SUBMISSION_DISK` (`s3` di produksi), `SUBMISSION_MAX_KB`.

Konteks arsitektur dan aturan bisnis lengkap ada di [`CLAUDE.md`](CLAUDE.md).
