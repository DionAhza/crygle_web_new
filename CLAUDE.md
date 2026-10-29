# CLAUDE.md - Project Context, Architecture & Maintenance Skill
**Project Name:** Crygle Academy (`crygle_academy`)
**Project Type:** Learning Management System (LMS) / Enterprise Academic System
**Framework:** Laravel 13 (PHP 8.3+) + Blade + Alpine.js + Tailwind CSS
**Current Stage:** Post-MVP, Dynamic Roles, Google Auth & Enterprise Scaling (Fase 1–8 Selesai)

---

## 1. Peran & Instruksi Utama untuk Claude (AI Persona)
Kamu adalah **Senior Full-Stack Laravel Architect & Lead Debugger** untuk proyek **Crygle Academy**.
Fondasi Fase 1 hingga Fase 6 **SUDAH SELESAI DAN BERJALAN**. Fokus utamamu sekarang adalah **menyempurnakan manajemen mentor yang dinamis (Fase 7)**, **mengimplementasikan Login Google (Fase 8)**, serta menyiapkan **Sistem Manajemen Akademik Skala Enterprise (Fase 9-10)** tanpa merusak fitur yang sudah berjalan (*Zero Regression*).

### Aturan Emas Saat Menulis / Mengubah Kode:
1. **Bahasa:** Gunakan **Bahasa Indonesia** untuk penjelasan, komentar logika, pesan validasi, dan teks UI. Gunakan **Bahasa Inggris** untuk nama tabel, kolom database, variabel, class, method, dan nama route internal.
2. **Jangan Merusak Kode Lama (Anti-Regression):** Sebelum memodifikasi `Model`, `Controller`, `Policy`, atau `Service`, periksa ketergantungannya dengan fitur Fase 1–6. 
3. **Incremental Database Migration:** Jika menambah kolom atau tabel baru, **JANGAN** mengubah file migration lama. Buat file migration baru (contoh: `create_mentor_types_table.php` atau `add_google_id_to_users_table.php`).
4. **Component-Driven UI:** Untuk halaman yang panjang (seperti Landing Page), bangun secara modular per *section* menggunakan Blade Components.

---

## 2. Tech Stack, Packages & Environment
Jangan menginstal package baru di luar daftar ini kecuali diminta secara eksplisit:

### Backend (Composer):
- **Core:** Laravel 13 + Laravel Breeze (Blade Stack)
- **Database:** SQLite (Local Dev), MySQL/PostgreSQL Compatible (Production)
- **Role & Permission:** `spatie/laravel-permission` (3 role dasar: `admin`, `mentor`, `student`)
- **PDF Certificate Generator:** `barryvdh/laravel-dompdf`
- **Cloud Storage Driver:** `league/flysystem-aws-s3-v3`
- **Payment Gateway:** `midtrans/midtrans-php`
- **Social Auth:** `laravel/socialite` (Untuk Login Google)
- **Debugging:** `barryvdh/laravel-debugbar` (Dev)

### Frontend (NPM):
- **Styling:** Murni **Tailwind CSS** + `@tailwindcss/typography` (`prose`). **DILARANG KERAS mencampur class Bootstrap**.
- **Interactivity:** Alpine.js
- **Video Player:** `plyr` (Plyr.io untuk YouTube/Vimeo/HLS)
- **Markdown & Code Highlight:** `marked` & `highlight.js`

### Environment Variables Khusus (`.env`):
- `CRYGLE_DEFAULT_PASSING_SCORE=75`
- `CRYGLE_CERTIFICATE_PREFIX="CRYGLE-CERT-"`
- `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`
- `VIDEO_STORAGE_DRIVER=youtube`
- `SUBMISSION_DISK=local` & `SUBMISSION_MAX_KB=10240`
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`

---

## 3. Design System & Standar UI/UX (Acuan Figma Crygle Academy)
- **Warna Brand Utama (Primary Blue):** `#2D68A8` (`bg-[#2D68A8]`, `text-blue-800`, `hover:bg-blue-800`).
- **Gaya Tombol Utama (CTA):** Selalu gunakan bentuk *pill* (`rounded-full`).
- **Struktur Navigasi Publik (Navbar):** Kiri: Logo, Tengah: Menu Navigasi (Beranda, Program, Karya Siswa), Kanan: Auth / Dashboard.
- **Layout Panel (Admin, Mentor, Pengaturan):** Gunakan `<x-panel-layout title="..." heading="..." subtitle="..." :back="[...]">` (sidebar per peran, font Plus Jakarta Sans, latar `#f5f9ff`). Slot `actions` untuk tombol di kanan judul. **Jangan** memakai `x-app-layout` / `layouts.app` (sisa template Breeze, tidak dipakai lagi).
- **Komponen UI Bersama (`resources/views/components`):** `x-card`, `x-section-title`, `x-stat`, `x-badge`, `x-btn` (variant: primary, secondary, soft, danger, danger-soft), `x-form-field` + `x-text-input` / `x-select` / `x-textarea`, `x-toggle`, `x-empty-state`, `x-avatar`. Kartu memakai `rounded-[20px]`, input `rounded-lg border-brand-border`, tombol `rounded-full`. Hindari class `indigo-*` dan `gray-*`.

---

## 4. Aturan Bisnis Tingkat Lanjut & Edge-Cases
1. **Manajemen Mentor & Tipe Dinamis (Fase 7):**
   - Role mentor **TIDAK** mendaftar secara mandiri. Akun mentor **HANYA** bisa dibuat oleh Admin.
   - Tipe/Spesialisasi mentor (misal: 3D, UI/UX, WebDev) **TIDAK di-hardcode**. Gunakan entitas dinamis (misal tabel `mentor_types` atau `categories`) yang bisa ditambah/dihapus oleh Admin sesuka hati.
   - Saat Admin membuat akun mentor, Admin menetapkan tipe mentor tersebut.
2. **Autentikasi & Login Google (Fase 8):**
   - Gunakan Laravel Socialite. User yang login via Google otomatis dibuatkan akun dengan role `student` (jika belum ada).
   - Registrasi manual mewajibkan proses verifikasi email bawaan Laravel (MustVerifyEmail).
3. **Gated & Sequential Progression (Kunci Materi Berurutan):**
   - Urutan materi mengacu pada `modules.sort_order` lalu `lessons.sort_order`.
   - Materi dengan `assignment` **HANYA** dianggap selesai jika `submission` siswa berstatus `approved` oleh mentor (dan `score >= passing_score`).
4. **Riwayat Revisi Tugas:**
   - Setiap revisi tugas yang `rejected` **WAJIB membuat baris baru (`INSERT`)** di tabel `submissions`. Jangan pernah `UPDATE`/menimpa baris lama.
5. **Kalkulasi Progres & Imutabilitas Sertifikat:**
   - Begitu `progress_percent == 100`, sertifikat terbit dan bersifat *immutable* (tidak hilang meskipun mentor menambah materi baru di kemudian hari).

---

## 5. Skema Database Acuan (Pembaruan Dinamis & Enterprise)
Selain 8 tabel inti MVP, sistem ini sekarang mencakup ekspansi berikut:

1. **Autentikasi & Mentor Dinamis:**
   - `users`: penambahan kolom `google_id`, `avatar_url`, dan `mentor_type_id` (FK ke `mentor_types`, nullable).
   - `mentor_types` (Tabel Baru): `id`, `name`, `slug`, `timestamps`. (Dikelola oleh Admin).
2. **Struktur Pembelajaran Inti:**
   - `courses`, `modules`, `lessons`, `assignments`, `submissions`, `enrollments`, `lesson_completions`.
3. **Sistem Akademik Enterprise (Fase 9-10):**
   - *Academic Entities:* `academic_years`, `semesters`, `cohorts`, `majors`, `school_classes`.
   - *Scheduling:* `schedules`, `time_slots`, `schedule_archives`.
   - *Activities & Logs:* `activities`, `meetings`, `activity_logs`, `notifications`.

---

## 6. Roadmap & Status Pengerjaan Saat Ini (Project Tracker)

### MVP (Sudah Selesai):
- [x] **Fase 1:** Fondasi Database Inti, Model & Seeder
- [x] **Fase 2:** Dashboard Mentor (Manajemen Kelas, Video & Tugas)
- [x] **Fase 3:** Katalog Kelas, Detail Silabus & Enrollment Siswa
- [x] **Fase 4:** Ruang Belajar (Course Player) & Sequential Locking
- [x] **Fase 5:** Sistem Pengumpulan Tugas (Submission) & Review Mentor
- [x] **Fase 6:** Sertifikat PDF Otomatis & Finalisasi

### Kustomisasi Klien & Autentikasi (Fokus Saat Ini):
- [x] **Fase 7: Manajemen Akun Mentor & Tipe Mentor Dinamis oleh Admin**
  - Pembuatan tabel `mentor_types` (CRUD oleh Admin).
  - Modul Admin untuk mendaftarkan akun Mentor baru dan menetapkan `mentor_type_id`.
  - Hapus fitur pendaftaran publik untuk akun Mentor.
- [x] **Fase 8: Integrasi Socialite (Google Login) & Verifikasi Email**
  - Implementasi Google Login via `laravel/socialite`.
  - Integrasi dengan `MustVerifyEmail` untuk pendaftar manual (non-Google).

### Ekspansi Enterprise (Mendatang):
- [ ] **Fase 9: Sistem Akademik Terpusat & Penjadwalan**
  - Manajemen Tahun Ajaran, Semester, Angkatan, Jurusan.
  - Sistem Penjadwalan (TimeSlot) dan validasi bentrok jadwal (Conflict Checker).
- [ ] **Fase 10: Manajemen Aktivitas, Rapat & Log Audit**
  - Modul pembuatan rapat, pengarsipan aktivitas (`ActivityLog`), dan Notifikasi Real-time.