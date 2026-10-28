<?php

namespace Tests\Feature\Student;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\Module;
use App\Models\Submission;
use App\Models\User;
use App\Services\CertificateService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    private User $mentor;

    private User $student;

    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->mentor = tap(User::factory()->create(['name' => 'Bu Mentor']), fn ($u) => $u->assignRole('mentor'));
        $this->student = tap(User::factory()->create(['name' => 'Siti Aminah', 'email' => 'siti@example.test']), fn ($u) => $u->assignRole('student'));
        $this->course = Course::factory()->create(['mentor_id' => $this->mentor->id, 'title' => 'Dasar Laravel', 'level' => 'basic', 'price' => 0]);

        $module = Module::factory()->create(['course_id' => $this->course->id]);
        Lesson::factory()->count(3)->create(['module_id' => $module->id]);
    }

    private function enrollment(array $state = [], bool $completed = true): Enrollment
    {
        $factory = Enrollment::factory();
        if ($completed) {
            $factory = $factory->completed();
        }

        return $factory->create(array_merge(['student_id' => $this->student->id, 'course_id' => $this->course->id], $state));
    }

    public function test_owner_can_download_certificate_pdf_when_course_is_completed(): void
    {
        $enrollment = $this->enrollment();

        $response = $this->actingAs($this->student)->get(route('certificates.download', $enrollment));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString(
            strtolower($enrollment->certificate_code).'.pdf',
            $response->headers->get('content-disposition'),
        );
    }

    public function test_certificate_cannot_be_downloaded_before_course_is_completed(): void
    {
        $enrollment = $this->enrollment(['progress_percent' => 60], completed: false);

        $this->actingAs($this->student)->get(route('certificates.download', $enrollment))->assertForbidden();
    }

    public function test_unpaid_enrollment_cannot_download_certificate(): void
    {
        $enrollment = $this->enrollment(['payment_status' => 'pending']);

        $this->actingAs($this->student)->get(route('certificates.download', $enrollment))->assertForbidden();
    }

    public function test_other_users_and_mentor_cannot_download_someone_elses_certificate(): void
    {
        $enrollment = $this->enrollment();
        $other = tap(User::factory()->create(), fn ($u) => $u->assignRole('student'));

        $this->actingAs($other)->get(route('certificates.download', $enrollment))->assertForbidden();
        $this->actingAs($this->mentor)->get(route('certificates.download', $enrollment))->assertForbidden();
    }

    public function test_download_requires_login(): void
    {
        $enrollment = $this->enrollment();

        $this->get(route('certificates.download', $enrollment))->assertRedirect(route('login'));
    }

    public function test_verification_page_is_public_and_shows_details_without_private_data(): void
    {
        $enrollment = $this->enrollment();

        $this->get(route('certificates.verify', $enrollment->certificate_code))
            ->assertOk()
            ->assertSee('Sertifikat Terverifikasi')
            ->assertSee('Siti Aminah')
            ->assertSee('Dasar Laravel')
            ->assertSee('Bu Mentor')
            ->assertSee($enrollment->certificate_code)
            ->assertDontSee('siti@example.test');
    }

    public function test_verification_of_unknown_code_returns_404_page(): void
    {
        $this->get(route('certificates.verify', 'CRYGLE-CERT-TIDAKADA'))
            ->assertNotFound()
            ->assertSee('Sertifikat Tidak Ditemukan');
    }

    public function test_verification_rejects_certificate_of_unpaid_or_incomplete_enrollment(): void
    {
        $enrollment = $this->enrollment(['payment_status' => 'failed']);

        $this->get(route('certificates.verify', $enrollment->certificate_code))->assertNotFound();
    }

    public function test_my_courses_page_shows_certificate_button_only_for_completed_courses(): void
    {
        $done = $this->enrollment();
        $otherCourse = Course::factory()->create(['mentor_id' => $this->mentor->id, 'title' => 'Kelas Berjalan']);
        $this->enrollment(['course_id' => $otherCourse->id, 'progress_percent' => 40], completed: false);

        $response = $this->actingAs($this->student)->get(route('student.courses'));

        $response->assertOk()
            ->assertSee(route('certificates.download', $done), false)
            ->assertSee('Unduh Sertifikat (PDF)', false);
        $this->assertSame(1, substr_count($response->getContent(), 'Unduh Sertifikat (PDF)'));
    }

    public function test_learn_page_shows_congratulation_banner_after_completion(): void
    {
        $enrollment = $this->enrollment();
        $lesson = $this->course->lessons()->first();

        $this->actingAs($this->student)->get(route('learn.show', [$this->course, $lesson]))
            ->assertOk()
            ->assertSee('Sertifikat Anda sudah siap diunduh')
            ->assertSee(route('certificates.download', $enrollment), false);
    }

    public function test_learn_page_hides_banner_before_completion(): void
    {
        $this->enrollment(['progress_percent' => 0], completed: false);
        $lesson = $this->course->lessons()->first();

        $this->actingAs($this->student)->get(route('learn.show', [$this->course, $lesson]))
            ->assertOk()
            ->assertDontSee('Sertifikat Anda sudah siap diunduh');
    }

    public function test_service_builds_indonesian_data_and_safe_filename(): void
    {
        $enrollment = $this->enrollment(['completed_at' => '2026-08-17 03:00:00']);
        $service = app(CertificateService::class);

        $data = $service->data($enrollment);

        $this->assertSame('17 Agustus 2026', $data['completedOn']);
        $this->assertSame('Dasar', $data['courseLevel']);
        $this->assertStringStartsWith('data:image/png;base64,', $data['qr']);
        $this->assertSame(3, $data['lessonCount']);
        $this->assertSame(
            'sertifikat-dasar-laravel-'.strtolower($enrollment->certificate_code).'.pdf',
            $service->filename($enrollment),
        );
    }

    private function gradedAssignments(array $scores): void
    {
        foreach ($this->course->lessons()->orderBy('lessons.id')->get()->take(count($scores)) as $i => $lesson) {
            $assignment = Assignment::factory()->create(['lesson_id' => $lesson->id, 'title' => 'Tugas '.($i + 1)]);
            Submission::factory()->create([
                'assignment_id' => $assignment->id, 'student_id' => $this->student->id,
                'status' => 'approved', 'score' => $scores[$i], 'reviewed_at' => now(),
            ]);
        }
    }

    public function test_assessment_rows_use_best_approved_score_and_average_total(): void
    {
        $enrollment = $this->enrollment();
        $this->gradedAssignments([80, 91]);

        $data = app(CertificateService::class)->data($enrollment);

        $this->assertCount(2, $data['assessments']);
        $this->assertSame([80, 91], $data['assessments']->pluck('score')->all());
        $this->assertSame(86, $data['totalScore']); // round(85.5)
    }

    public function test_certificate_without_graded_assignments_is_single_page_and_with_them_two_pages(): void
    {
        $enrollment = $this->enrollment();
        $service = app(CertificateService::class);

        $one = $service->render($enrollment)->output();
        $this->assertSame(1, preg_match_all('/\/Type\s*\/Page\b/', $one));

        $this->gradedAssignments([80, 91, 100]);
        $two = $service->render($enrollment->fresh())->output();
        $this->assertSame(2, preg_match_all('/\/Type\s*\/Page\b/', $two));

        if ($dir = getenv('CERT_PREVIEW_DIR')) {
            file_put_contents($dir.'/preview.pdf', $two);
        }
    }
}
