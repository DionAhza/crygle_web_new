# CLAUDE.md - Project Context, Architecture & Maintenance Skill
**Project Name:** Crygle Academy (`crygle_academy`)
**Project Type:** Learning Management System (LMS) / E-Course Platform (Mirip Dicoding)
**Framework:** Laravel 13 (PHP 8.3+) + Blade + Alpine.js + Tailwind CSS
**Current Stage:** Pre-Deployment Refinement, Schema Iteration & UI Slicing (Fase 1–6 Selesai, Siap Kustomisasi)

---

## 1. Peran & Instruksi Utama untuk Claude (AI Persona)
Kamu adalah **Senior Full-Stack Laravel Architect & Lead Debugger** untuk proyek **Crygle Academy**.
Fitur dasar Fase 1 hingga Fase 6 sudah berjalan, dan saat ini proyek masih berada di tahap **Pre-Deployment (Local Development)**.

### Aturan Kerja Saat Menulis / Merombak Kode:
1. **Bahasa:** Gunakan **Bahasa Indonesia** untuk penjelasan, komentar logika, pesan validasi, dan teks UI. Gunakan **Bahasa Inggris** untuk nama tabel, kolom database, variabel, class, method, dan route.
2. **Boleh Merombak Migration & Seeder (`migrate:fresh --seed`):** Karena aplikasi belum naik ke produksi, jika ada perubahan struktur tabel (seperti penambahan kolom spesialisasi mentor atau kategori kelas), kamu **DIPERBOLEHKAN** mengedit langsung file migration utama dan memperbarui `DatabaseSeeder.php`, lalu menginstruksikan `php artisan migrate:fresh --seed`.
3. **Jaga Kompatibilitas Fitur Fase 1–6:** Saat merombak tabel atau menambah fitur baru, pastikan alur yang sudah ada (seperti Sequential Locking, Submission Review, dan PDF Certificate) tetap sinkron dan tidak menimbulkan error pada Controller/Policy/View terkait.
4. **Component-Driven UI:** Untuk halaman yang panjang (seperti Landing Page), kerjakan secara bertahap per *section* menggunakan Blade Components agar kode utuh dan tidak terpotong.

---

## 2. Tech Stack, Packages & Environment
Jangan menginstal package baru di luar daftar ini kecuali diminta secara eksplisit:

### Backend (Composer):
- **Core:** Laravel 13 + Laravel Breeze (Blade Stack)
- **Database:** SQLite (Local Dev), MySQL/PostgreSQL Compatible (Production)
- **Role & Permission:** `spatie/laravel-permission`
- **PDF Certificate Generator:** `barryvdh/laravel-dompdf`
- **Cloud Storage Driver:** `league/flysystem-aws-s3-v3`
- **Payment Gateway:** `midtrans/midtrans-php`
- **Debugging:** `barryvdh/laravel-debugbar` (Dev)

### Frontend (NPM):
- **Styling:** Murni **Tailwind CSS** + `@tailwindcss/typography` (`prose`). **DILARANG mencampur class Bootstrap** (seperti `btn`, `btn-primary`, `justify-content-between`) ke dalam file Blade.
- **Interactivity:** Alpine.js
- **Video Player:** `plyr` (Plyr.io untuk YouTube/Vimeo/HLS)
- **Markdown & Code Highlight:** `marked` & `highlight.js`

### Environment Variables Khusus (`.env`):
- `CRYGLE_DEFAULT_PASSING_SCORE=75`
- `CRYGLE_CERTIFICATE_PREFIX="CRYGLE-CERT-"`
- `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`
- `VIDEO_STORAGE_DRIVER=youtube`
- `SUBMISSION_DISK=local` (gunakan `s3` di produksi) & `SUBMISSION_MAX_KB=10240`

---

## 3. Design System & Standar UI/UX (Acuan Figma Crygle Academy)
- **Warna Brand Utama (Primary Blue):** `#2D68A8` (`bg-[#2D68A8]`, `text-blue-800`, `hover:bg-blue-800`) dipadukan dengan aksen `text-blue-600`.
- **Gaya Tombol Utama (CTA):** Bentuk *pill* (`rounded-full`, contoh: `bg-[#2D68A8] text-white px-6 py-2.5 rounded-full hover:bg-blue-800 transition`).
- **Struktur Navigasi Publik (Navbar):**
  - Kiri: Logo SVG + Teks `Crygle Academy` (`text-blue-800 font-bold leading-tight`).
  - Tengah: `Beranda`, `Program` (dropdown kategori: 3D Design, UI/UX Design, Web Development), `Karya Siswa`, `Tentang Crygle`.
  - Kanan: `Masuk` (link teks biru) & `Lihat Kelas` (tombol solid `rounded-full` `#2D68A8`), atau tombol `Dashboard` jika sudah login.

---

## 4. Aturan Bisnis Utama (Core Business Logic)
1. **Multi-Role & Spesialisasi Mentor (`3d`, `uiux`, `webdev`):**
   - Role utama Spatie tetap menggunakan 3 role dasar agar route & middleware tetap bersih: `admin`, `mentor`, `student`.
   - Setiap user ber-role `mentor` memiliki spesialisasi bidang pada kolom `users.specialization` (`3d`, `uiux`, `webdev`) — contohnya: **Mentor 3D**, **Mentor UI/UX**, dan **Mentor WebDev**.
   - Setiap kelas (`courses`) memiliki kolom `category` (`3d`, `uiux`, `webdev`). Secara default, seorang mentor membuat kelas dan menilai tugas sesuai bidang spesialisasinya (`course.mentor_id === auth()->id()`).
2. **Gated & Sequential Progression (Kunci Materi Berurutan):**
   - Siswa wajib menyelesaikan materi secara berurutan (`modules.sort_order` -> `lessons.sort_order`).
   - Materi yang memiliki `assignment` **BELUM** dihitung selesai (`lesson_completions`) sampai `submission` siswa berstatus `approved` oleh mentor (atau `score >= passing_score`).
3. **Riwayat Revisi Tugas & Anti-Spam Submission:**
   - Saat siswa mengirim ulang revisi tugas yang sebelumnya `rejected`, **JANGAN timpa (overwrite)** baris lama di tabel `submissions`. Buat *record* baru (`INSERT`) agar histori pengiriman ke-1, ke-2, dst. tetap tersimpan.
   - Kunci form pengumpulan jika siswa masih memiliki `submission` berstatus `pending` pada tugas tersebut.
4. **Kalkulasi Progres & Imutabilitas Sertifikat:**
   - Setiap penambahan di `lesson_completions` menghitung ulang `enrollments.progress_percent`. Cegah *Division by Zero* jika total lesson masih 0.
   - Jika `progress_percent == 100`, isi `completed_at` dan generate `certificate_code` unik (`CRYGLE-CERT-{RANDOM}`).
   - Sertifikat yang sudah terbit tetap sah (*immutable*) meskipun mentor menambah materi baru di kemudian hari.

---

## 5. Skema Database Acuan (8 Tabel Utama)
1. **`users`**: `id`, `name`, `email`, `password`, `avatar_url`, `specialization` (`enum/string`: `3d`, `uiux`, `webdev`, nullable untuk student/admin), `timestamps` + Spatie `HasRoles` (`admin`, `mentor`, `student`).
2. **`courses`**: `id`, `mentor_id` (FK `users.id`), `category` (`enum`: `3d`, `uiux`, `webdev`), `title`, `slug` (unique), `description`, `thumbnail_url`, `price` (decimal), `level` (`basic`, `intermediate`, `advanced`), `is_published` (boolean), `timestamps`.
3. **`modules`**: `id`, `course_id` (FK `courses.id`), `title`, `sort_order` (unsignedInteger), `timestamps`.
4. **`lessons`**: `id`, `module_id` (FK `modules.id`), `title`, `slug`, `video_url`, `duration_seconds`, `content_text` (longText), `sort_order`, `is_free_preview` (boolean), `timestamps`.
5. **`enrollments`**: `id`, `student_id` (FK `users.id`), `course_id` (FK `courses.id`), `payment_status` (`pending`, `paid`, `failed`), `progress_percent` (0-100), `completed_at` (nullable), `certificate_code` (unique, nullable), `timestamps`. Unique: `['student_id', 'course_id']`.
6. **`lesson_completions`**: `id`, `student_id` (FK `users.id`), `lesson_id` (FK `lessons.id`), `completed_at`. Unique: `['student_id', 'lesson_id']`.
7. **`assignments`**: `id`, `lesson_id` (FK `lessons.id`), `title`, `instructions` (text), `submission_type` (`file`, `repo_link`, `text`), `passing_score` (default 75), `timestamps`.
8. **`submissions`**: `id`, `assignment_id` (FK `assignments.id`), `student_id` (FK `users.id`), `reviewer_id` (FK `users.id`, nullable), `attachment_url` (nullable), `student_note` (text, nullable), `status` (`pending`, `approved`, `rejected`), `score` (nullable), `mentor_feedback` (text, nullable), `reviewed_at` (timestamp, nullable), `timestamps`.

---

## 6. Roadmap & Status Pengerjaan Saat Ini (Project Tracker)
- [x] **Fase 1: Fondasi Database, Model & Seeder**
- [x] **Fase 2: Dashboard Mentor (Manajemen Kelas, Video & Tugas)**
- [x] **Fase 3: Katalog Kelas, Detail Silabus & Enrollment Siswa**
- [x] **Fase 4: Ruang Belajar (Course Player) & Sequential Locking**
- [x] **Fase 5: Sistem Pengumpulan Tugas (Submission) & Review Mentor** (`SUBMISSION_DISK` & `SUBMISSION_MAX_KB`)
- [x] **Fase 6: Sertifikat PDF Otomatis & Finalisasi**
  - `CertificateService`, `CertificateController` (`download` + `verify`), `EnrollmentPolicy@downloadCertificate`.
  - Rute: `GET /sertifikat/{enrollment}/unduh` dan `GET /sertifikat/verifikasi/{code}`.
- [ ] **Fase 7: Kustomisasi Spesialisasi Mentor (3D, UI/UX, WebDev) & Penyempurnaan UI Figma**
  - Update migration & seeder untuk menambahkan `Mentor 3D`, `Mentor UI/UX`, dan `Mentor WebDev` beserta kategori kelasnya.
  - Slicing Landing Page (`welcome.blade.php`), dropdown Program (3D, UI/UX, WebDev), dan halaman pendukung sesuai Figma.