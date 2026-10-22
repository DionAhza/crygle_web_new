<?php

namespace Tests\Feature\Student;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Submission;
use App\Models\User;
use App\Services\CourseProgressService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User $mentor;
    private User $student;
    private Course $course;
    private Enrollment $enrollment;
    private Lesson $l1;
    private Lesson $l2;
    private Lesson $l3;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        Storage::fake('local');
        config(['crygle.submission_disk' => 'local']);

        $this->mentor = $this->user('mentor');
        $this->student = $this->user('student');
        $this->course = Course::factory()->create(['mentor_id' => $this->mentor->id, 'price' => 0]);

        $module = Module::factory()->create(['course_id' => $this->course->id, 'sort_order' => 1]);
        $this->l1 = Lesson::factory()->create(['module_id' => $module->id, 'sort_order' => 1]);
        $this->l2 = Lesson::factory()->create(['module_id' => $module->id, 'sort_order' => 2]);
        $this->l3 = Lesson::factory()->create(['module_id' => $module->id, 'sort_order' => 3]);
        Assignment::factory()->create(['lesson_id' => $this->l2->id]);
        Assignment::factory()->create(['lesson_id' => $this->l3->id]);

        $this->enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id, 'course_id' => $this->course->id, 'payment_status' => 'paid',
        ]);

        // L1 selesai, sehingga L2 (bertugas) terbuka dan L3 masih terkunci.
        app(CourseProgressService::class)->markCompleted($this->student, $this->l1, $this->enrollment);
    }

    private function user(string $role): User
    {
        return tap(User::factory()->create(), fn ($u) => $u->assignRole($role));
    }

    private function url(Lesson $lesson): string
    {
        return route('student.submissions.store', [$this->course, $lesson]);
    }

    private function repoLink(array $extra = []): array
    {
        return array_merge(['attachment_url' => 'https://github.com/siswa/proyek', 'student_note' => 'Mohon dicek.'], $extra);
    }

    public function test_student_can_submit_repo_link_and_lesson_stays_incomplete(): void
    {
        $this->actingAs($this->student)->post($this->url($this->l2), $this->repoLink())
            ->assertRedirect(route('learn.show', [$this->course, $this->l2]))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $this->l2->assignment->id,
            'student_id' => $this->student->id,
            'attachment_url' => 'https://github.com/siswa/proyek',
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('lesson_completions', ['lesson_id' => $this->l2->id]);
        $this->assertSame(33, $this->enrollment->fresh()->progress_percent);
    }

    public function test_invalid_or_unsafe_link_is_rejected(): void
    {
        foreach (['bukan url', 'javascript:alert(1)', 'ftp://x.test/a'] as $bad) {
            $this->actingAs($this->student)->post($this->url($this->l2), $this->repoLink(['attachment_url' => $bad]))
                ->assertSessionHasErrors('attachment_url');
        }

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_cannot_submit_to_a_locked_lesson(): void
    {
        $this->actingAs($this->student)->post($this->url($this->l3), $this->repoLink())
            ->assertSessionHasErrors('submission');

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_cannot_submit_while_previous_submission_is_pending(): void
    {
        $this->actingAs($this->student);
        $this->post($this->url($this->l2), $this->repoLink())->assertSessionHasNoErrors();
        $this->post($this->url($this->l2), $this->repoLink(['attachment_url' => 'https://github.com/siswa/lain']))
            ->assertSessionHasErrors('submission');

        $this->assertDatabaseCount('submissions', 1);
    }

    public function test_resubmission_after_rejection_inserts_new_row_and_keeps_history(): void
    {
        $first = Submission::factory()->rejected(50)->create([
            'assignment_id' => $this->l2->assignment->id,
            'student_id' => $this->student->id,
            'reviewer_id' => $this->mentor->id,
        ]);

        $this->actingAs($this->student)->post($this->url($this->l2), $this->repoLink(['attachment_url' => 'https://github.com/siswa/revisi']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('submissions', 2);

        // Baris lama tidak tertimpa: status, skor, dan masukan mentor tetap utuh.
        $first->refresh();
        $this->assertSame('rejected', $first->status);
        $this->assertSame(50, $first->score);
        $this->assertSame('Silakan perbaiki dan kirim ulang.', $first->mentor_feedback);

        $this->assertDatabaseHas('submissions', ['attachment_url' => 'https://github.com/siswa/revisi', 'status' => 'pending']);
    }

    public function test_cannot_submit_again_after_approval(): void
    {
        Submission::factory()->approved()->create([
            'assignment_id' => $this->l2->assignment->id, 'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->student)->post($this->url($this->l2), $this->repoLink())
            ->assertSessionHasErrors('submission');

        $this->assertDatabaseCount('submissions', 1);
    }

    public function test_file_assignment_stores_upload_on_configured_disk(): void
    {
        $this->l2->assignment->update(['submission_type' => 'file']);

        $this->actingAs($this->student)->post($this->url($this->l2), [
            'attachment' => UploadedFile::fake()->create('tugas.pdf', 100, 'application/pdf'),
        ])->assertSessionHasNoErrors();

        $submission = Submission::firstOrFail();
        $this->assertStringStartsWith("submissions/{$this->l2->assignment->id}/{$this->student->id}/", $submission->attachment_url);
        Storage::disk('local')->assertExists($submission->attachment_url);
    }

    public function test_file_assignment_rejects_wrong_type_missing_and_oversized_files(): void
    {
        $this->l2->assignment->update(['submission_type' => 'file']);
        config(['crygle.submission_max_kb' => 100]);
        $this->actingAs($this->student);

        $this->post($this->url($this->l2), ['attachment' => UploadedFile::fake()->create('virus.exe', 50, 'application/x-msdownload')])
            ->assertSessionHasErrors('attachment');
        $this->post($this->url($this->l2), ['attachment' => UploadedFile::fake()->create('besar.pdf', 200, 'application/pdf')])
            ->assertSessionHasErrors('attachment');
        $this->post($this->url($this->l2), [])->assertSessionHasErrors('attachment');

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_text_assignment_requires_a_real_answer(): void
    {
        $this->l2->assignment->update(['submission_type' => 'text']);
        $this->actingAs($this->student);

        $this->post($this->url($this->l2), ['student_note' => 'abc'])->assertSessionHasErrors('student_note');
        $this->post($this->url($this->l2), ['student_note' => 'Jawaban saya adalah routing dipetakan di routes/web.php.'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('submissions', ['student_id' => $this->student->id, 'attachment_url' => null, 'status' => 'pending']);
    }

    public function test_only_paid_enrolled_students_can_submit(): void
    {
        $stranger = $this->user('student');
        $this->actingAs($stranger)->post($this->url($this->l2), $this->repoLink())->assertForbidden();

        $this->enrollment->update(['payment_status' => 'pending']);
        $this->actingAs($this->student)->post($this->url($this->l2), $this->repoLink())->assertForbidden();

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_mentor_cannot_use_student_submission_endpoint(): void
    {
        $this->actingAs($this->mentor)->post($this->url($this->l2), $this->repoLink())->assertForbidden();
    }

    public function test_lesson_without_assignment_or_from_other_course_is_404(): void
    {
        $this->actingAs($this->student)->post($this->url($this->l1), $this->repoLink())->assertNotFound();

        $foreign = Lesson::factory()->create();
        $this->actingAs($this->student)->post($this->url($foreign), $this->repoLink())->assertNotFound();
    }

    public function test_learn_page_shows_form_then_pending_state_and_history(): void
    {
        $this->actingAs($this->student);

        $this->get(route('learn.show', [$this->course, $this->l2]))
            ->assertOk()->assertSee('Kumpulkan Tugas')->assertSee('Kirim Tugas');

        $this->post($this->url($this->l2), $this->repoLink());

        $this->get(route('learn.show', [$this->course, $this->l2]))
            ->assertOk()
            ->assertSee('menunggu review mentor')
            ->assertSee('Pengiriman ke-1')
            ->assertDontSee('Kirim Tugas');
    }

    public function test_learn_page_shows_mentor_feedback_and_revision_form_after_rejection(): void
    {
        Submission::factory()->rejected(40)->create([
            'assignment_id' => $this->l2->assignment->id, 'student_id' => $this->student->id,
            'reviewer_id' => $this->mentor->id, 'mentor_feedback' => 'Struktur folder belum rapi.',
        ]);

        $this->actingAs($this->student)->get(route('learn.show', [$this->course, $this->l2]))
            ->assertOk()->assertSee('Struktur folder belum rapi.')->assertSee('Kirim Revisi');
    }

    public function test_submission_files_can_only_be_downloaded_by_owner_student_or_course_mentor(): void
    {
        $this->l2->assignment->update(['submission_type' => 'file']);
        $path = 'submissions/1/1/tugas.pdf';
        Storage::disk('local')->put($path, 'isi berkas');
        $submission = Submission::factory()->create([
            'assignment_id' => $this->l2->assignment->id, 'student_id' => $this->student->id, 'attachment_url' => $path,
        ]);
        $url = route('submissions.download', $submission);

        $this->actingAs($this->student)->get($url)->assertOk()->assertDownload();
        $this->actingAs($this->mentor)->get($url)->assertOk();

        $this->actingAs($this->user('student'))->get($url)->assertForbidden();
        $this->actingAs($this->user('mentor'))->get($url)->assertForbidden();
    }

    public function test_download_requires_login(): void
    {
        $submission = Submission::factory()->create([
            'assignment_id' => $this->l2->assignment->id, 'student_id' => $this->student->id,
        ]);

        $this->get(route('submissions.download', $submission))->assertRedirect('/login');
    }
}
