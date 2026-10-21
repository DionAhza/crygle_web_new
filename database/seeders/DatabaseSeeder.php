<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Semua akun demo memakai password: "password"
        $admin = User::factory()->create(['name' => 'Admin Crygle', 'email' => 'admin@crygle.test']);
        $admin->assignRole('admin');

        $mentor = User::factory()->create(['name' => 'Mentor Crygle', 'email' => 'mentor@crygle.test']);
        $mentor->assignRole('mentor');

        $student = User::factory()->create(['name' => 'Siswa Demo', 'email' => 'student@crygle.test']);
        $student->assignRole('student');

        $catalog = [
            ['Dasar Pemrograman Web dengan Laravel', 'basic', 0, [
                'Pengenalan Laravel' => ['Apa itu Laravel?', 'Instalasi dan Struktur Proyek', 'Routing Dasar'],
                'Blade dan Database' => ['Blade Template', 'Migration dan Eloquent', 'CRUD Sederhana'],
            ]],
            ['Membangun REST API dengan Laravel', 'intermediate', 199000, [
                'Fondasi API' => ['Konsep REST', 'API Resource', 'Autentikasi Sanctum'],
                'API Produksi' => ['Validasi dan Error Handling', 'Testing API', 'Deployment'],
            ]],
        ];

        foreach ($catalog as [$title, $level, $price, $modules]) {
            $course = Course::create([
                'mentor_id' => $mentor->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => "Kelas {$title} untuk pelajar vokasi dan pemula yang ingin belajar secara bertahap.",
                'price' => $price,
                'level' => $level,
                'is_published' => true,
            ]);

            $moduleOrder = 1;
            foreach ($modules as $moduleTitle => $lessons) {
                $module = Module::create([
                    'course_id' => $course->id,
                    'title' => $moduleTitle,
                    'sort_order' => $moduleOrder++,
                ]);

                $lessonOrder = 1;
                foreach ($lessons as $lessonTitle) {
                    $lesson = Lesson::create([
                        'module_id' => $module->id,
                        'title' => $lessonTitle,
                        'slug' => Str::slug($lessonTitle),
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 600,
                        'content_text' => "## {$lessonTitle}\n\nMateri bacaan untuk **{$lessonTitle}**.",
                        'sort_order' => $lessonOrder,
                        'is_free_preview' => $moduleOrder === 2 && $lessonOrder === 1,
                    ]);

                    // Lesson terakhir tiap modul memiliki tugas.
                    if ($lessonOrder === count($lessons)) {
                        Assignment::create([
                            'lesson_id' => $lesson->id,
                            'title' => "Tugas: {$lessonTitle}",
                            'instructions' => 'Kumpulkan tautan repository GitHub hasil pengerjaanmu.',
                            'submission_type' => 'repo_link',
                            'passing_score' => config('crygle.default_passing_score'),
                        ]);
                    }

                    $lessonOrder++;
                }
            }
        }

        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => Course::where('slug', Str::slug($catalog[0][0]))->value('id'),
            'payment_status' => 'paid',
        ]);
    }
}
