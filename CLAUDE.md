# CLAUDE.md - Project Context & AI Developer Skill
**Project Name:** Crygle Academy (`crygle_academy`)
**Project Type:** Learning Management System (LMS) / E-Course Platform (Mirip Dicoding)
**Framework:** Laravel 11 (PHP 8.2+) + Blade + Alpine.js + Tailwind CSS

---

## 1. Peran & Instruksi Utama untuk Claude (AI Persona)
Kamu adalah **Senior Full-Stack Laravel Architect** untuk proyek **Crygle Academy**. 
Tugasmu adalah menulis kode yang bersih, modular, aman, bebas dari *N+1 Query Problem*, dan siap produksi (*production-ready*).
- **Selalu gunakan Bahasa Indonesia** untuk penjelasan dan teks antarmuka (UI), namun gunakan **Bahasa Inggris** untuk penamaan tabel, kolom database, variabel, class, method, dan route.
- Jangan pernah menginstal ulang atau menyarankan package di luar daftar Tech Stack di bawah kecuali diminta secara eksplisit.
- Setiap kali selesai mengerjakan sebuah fitur/fase, ingatkan user untuk mengupdate bagian **Status Pengerjaan (Project Progress)** di bagian bawah dokumen ini.

---

## 2. Tech Stack & Packages yang Sudah Terinstal
Asumsikan seluruh package berikut **SUDAH TERINSTAL** di dalam proyek:

### Backend (Composer):
- **Core:** Laravel 11 + Laravel Breeze (Blade Stack)
- **Database:** SQLite (Local Development), kompatibel dengan MySQL/PostgreSQL (Production)
- **Role & Permission:** `spatie/laravel-permission` (Menggunakan 3 role: `admin`, `mentor`, `student`)
- **PDF Certificate Generator:** `barryvdh/laravel-dompdf`
- **Cloud Storage Driver:** `league/flysystem-aws-s3-v3` (Untuk upload tugas siswa & thumbnail)
- **Payment Gateway:** `midtrans/midtrans-php`
- **Debugging:** `barryvdh/laravel-debugbar` (Dev)

### Frontend (NPM):
- **Styling:** Tailwind CSS + `@tailwindcss/typography` (Gunakan class `prose` untuk render materi bacaan/instruksi tugas)
- **Interactivity:** Alpine.js (Bawaan Breeze)
- **Video Player:** `plyr` (Plyr.io untuk membungkus video materi YouTube/Vimeo/HLS)
- **Markdown & Code Highlight:** `marked` & `highlight.js`

### Environment Variables Khusus (`.env`):
- `CRYGLE_DEFAULT_PASSING_SCORE=75`
- `CRYGLE_CERTIFICATE_PREFIX="CRYGLE-CERT-"`
- `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`
- `VIDEO_STORAGE_DRIVER=youtube`

---

## 3. Aturan Bisnis Utama (Core Business Logic)
Crygle Academy bukan sekadar web streaming video, melainkan LMS berbasis evaluasi ketat seperti Dicoding:
1. **Multi-Role System (`spatie/laravel-permission`):**
   - `admin`: Mengelola semua user, kategori, transaksi, dan persetujuan publikasi kelas.
   - `mentor`: Membuat kelas (`courses`), menyusun bab (`modules`), mengunggah video materi (`lessons`), membuat soal tugas (`assignments`), dan memeriksa/menilai tugas siswa (`submissions`).
   - `student`: Membeli/mendaftar kelas (`enrollments`), menonton video materi, mengumpulkan tugas, dan mengunduh sertifikat PDF.
2. **Gated & Sequential Progression (Kunci Materi Berurutan):**
   - Siswa harus menyelesaikan materi secara berurutan (`sort_order`).
   - Jika suatu `lesson` memiliki tugas (`assignment`), maka materi tersebut **BELUM** dihitung selesai (`lesson_completions`) sampai tugas yang dikumpulkan siswa (`submissions`) diberi status `approved` oleh Mentor (atau skor >= `passing_score`).
   - Materi berikutnya tetap terkunci sebelum materi saat ini selesai.
3. **Riwayat Revisi Tugas (Submission History):**
   - Ketika tugas siswa ditolak (`rejected`) oleh mentor dan siswa mengirim ulang revisi, **JANGAN timpa (overwrite)** baris data lama di tabel `submissions`.
   - Buat *record* baru (INSERT) agar histori pengiriman ke-1, ke-2, dst., beserta *feedback* mentor sebelumnya tetap tersimpan dan bisa dilihat di halaman detail tugas.
4. **Kalkulasi Progres & Sertifikat Otomatis:**
   - Setiap kali ada penambahan di `lesson_completions`, hitung ulang `progress_percent` pada tabel `enrollments`:
     `(Jumlah lesson selesai oleh siswa di kelas tersebut / Total lesson di kelas tersebut) * 100`.
   - Jika `progress_percent == 100`, isi kolom `completed_at` dan generate `certificate_code` unik (`CRYGLE-CERT-{RANDOM}`), lalu buka akses tombol Download Sertifikat PDF (`barryvdh/laravel-dompdf`).

---

## 4. Skema Database Acuan (8 Tabel Utama)
Selalu patuhi penamaan tabel dan kolom berikut saat menulis Migration, Model, maupun Query Eloquent:

1. **`users`**: `id`, `name`, `email`, `password`, `avatar_url`, `timestamps` + relasi role dari `spatie/laravel-permission` (`admin`, `mentor`, `student`).
2. **`courses`**: `id`, `mentor_id` (FK `users.id`), `title`, `slug` (unique), `description`, `thumbnail_url`, `price` (decimal), `level` (`basic`, `intermediate`, `advanced`), `is_published` (boolean), `timestamps`.
3. **`modules`**: `id`, `course_id` (FK `courses.id`), `title`, `sort_order` (unsignedInteger), `timestamps`.
4. **`lessons`**: `id`, `module_id` (FK `modules.id`), `title`, `slug`, `video_url`, `duration_seconds`, `content_text` (longText), `sort_order`, `is_free_preview` (boolean), `timestamps`.
5. **`enrollments`**: `id`, `student_id` (FK `users.id`), `course_id` (FK `courses.id`), `payment_status` (`pending`, `paid`, `failed`), `progress_percent` (0-100), `completed_at` (nullable), `certificate_code` (unique, nullable), `timestamps`. Unique index: `['student_id', 'course_id']`.
6. **`lesson_completions`**: `id`, `student_id` (FK `users.id`), `lesson_id` (FK `lessons.id`), `completed_at`. Unique index: `['student_id', 'lesson_id']`.
7. **`assignments`**: `id`, `lesson_id` (FK `lessons.id`), `title`, `instructions` (text), `submission_type` (`file`, `repo_link`, `text`), `passing_score` (default 75), `timestamps`.
8. **`submissions`**: `id`, `assignment_id` (FK `assignments.id`), `student_id` (FK `users.id`), `reviewer_id` (FK `users.id`, nullable), `attachment_url` (nullable), `student_note` (text, nullable), `status` (`pending`, `approved`, `rejected`), `score` (nullable), `mentor_feedback` (text, nullable), `reviewed_at` (timestamp, nullable), `timestamps`.

---

## 5. Standar Penulisan Kode (Coding Guidelines)
- **Laravel 11 Structure:** Ingat bahwa di Laravel 11 tidak ada `app/Http/Kernel.php`. Pendaftaran middleware alias dilakukan di `bootstrap/app.php`.
- **Query Optimization:** Selalu gunakan *Eager Loading* (`with(['modules.lessons', 'mentor'])`) untuk mencegah *N+1 Query*.
- **Security & Authorization:** Gunakan Middleware Spatie (`role:mentor`, `role:student`, `role:admin`) pada `routes/web.php`, serta pastikan mentor hanya bisa mengedit kelas dan menilai tugas dari kelas miliknya sendiri.
- **Blade & UI:** Buat tampilan modern dan responsif menggunakan Tailwind CSS dengan skema warna profesional (misal: Indigo/Slate).

---

## 6. Roadmap & Status Pengerjaan Saat Ini (Project Tracker)
- [x] **Fase 1: Fondasi Database, Model & Seeder**
- [x] **Fase 2: Dashboard Mentor (Manajemen Kelas, Video & Tugas)**
- [ ] **Fase 3: Katalog Kelas, Detail Silabus & Enrollment Siswa**
- [ ] **Fase 4: Ruang Belajar (Course Player) & Sequential Locking**
- [ ] **Fase 5: Sistem Pengumpulan Tugas (Submission) & Review Mentor**
- [ ] **Fase 6: Sertifikat PDF Otomatis & Finalisasi**