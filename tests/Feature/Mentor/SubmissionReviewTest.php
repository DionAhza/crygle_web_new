<?php

namespace Tests\Feature\Mentor;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Submission;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $mentor;
    private User $student;
    private Course $course;
    private Enrollment $enrollment;
    private Lesson $l1;
    private Lesson $l2;
    private Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->mentor = $this->user('mentor');
        $this->student = $this->user('student', ['name' => 'Budi Santoso']);
        $this->course = Course::factory()->create(['mentor_id' => $this->mentor->id, 'price' => 0]);

        $module = Module::factory()->create(['course_id' => $this->course->id, 'sort_order' => 1]);
        $this->l1 = Lesson::factory()->create(['module_id' => $module->id, 'sort_order' => 1]);
        $this->l2 = Lesson::factory()->create(['module_id' => $module->id, 'sort_order' => 2]);
        Assignment::factory()->create(['lesson_id' => $this->l1->id, 'passing_score' => 75]);
        Assignment::factory()->create(['lesson_id' => $this->l2->id, 'passing_score' => 75]);

        $this->enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id, 'course_id' => $this->course->id, 'payment_status' => 'paid',
        ]);

        $this->submission = Submission::factory()->create([
            'assignment_id' => $this->l1->assignment->id, 'student_id' => $this->student->id,
        ]);
    }

    private function user(string $role, array $attrs = []): User
    {
        return tap(User::factory()->create($attrs), fn ($u) => $u->assignRole($role));
    }

    private function review(Submission $submission, array $data)
    {
        return $this->patch(route('mentor.submissions.review', $submission), $data);
    }

    public function test_only_mentors_can_open_review_pages(): void
    {
        $this->get(route('mentor.submissions.index'))->assertRedirect('/login');
        $this->actingAs($this->student)->get(route('mentor.submissions.index'))->assertForbidden();
        $this->actingAs($this->mentor)->get(route('mentor.submissions.index'))->assertOk()->assertSee('Review Tugas');
    }

    public function test_index_lists_only_submissions_from_own_courses(): void
    {
        $otherMentor = $this->user('mentor');
        $otherCourse = Course::factory()->create(['mentor_id' => $otherMentor->id]);
        $otherLesson = Lesson::factory()->create(['module_id' => Module::factory()->create(['course_id' => $otherCourse->id])->id]);
        $otherAssignment = Assignment::factory()->create(['lesson_id' => $otherLesson->id]);
        Submission::factory()->create([
            'assignment_id' => $otherAssignment->id, 'student_id' => $this->user('student', ['name' => 'Citra Lestari'])->id,
        ]);

        $this->actingAs($this->mentor)->get(route('mentor.submissions.index'))
            ->assertOk()->assertSee('Budi Santoso')->assertDontSee('Citra Lestari');
    }

    public function test_index_filters_by_status(): void
    {
        Submission::factory()->approved()->create([
            'assignment_id' => $this->l2->assignment->id,
            'student_id' => $this->user('student', ['name' => 'Dewi Anggraini'])->id,
        ]);

        $this->actingAs($this->mentor);
        $this->get(route('mentor.submissions.index'))->assertSee('Budi Santoso')->assertDontSee('Dewi Anggraini');
        $this->get(route('mentor.submissions.index', ['status' => 'approved']))->assertSee('Dewi Anggraini')->assertDontSee('Budi Santoso');
        $this->get(route('mentor.submissions.index', ['status' => 'all']))->assertSee('Dewi Anggraini')->assertSee('Budi Santoso');
    }

    public function test_show_page_displays_submission_and_review_form(): void
    {
        $this->actingAs($this->mentor)->get(route('mentor.submissions.show', $this->submission))
            ->assertOk()->assertSee('Budi Santoso')->assertSee($this->submission->attachment_url)->assertSee('Simpan Penilaian');
    }

    public function test_other_mentor_cannot_view_or_review(): void
    {
        $other = $this->user('mentor');

        $this->actingAs($other)->get(route('mentor.submissions.show', $this->submission))->assertForbidden();
        $this->actingAs($other);
        $this->review($this->submission, ['decision' => 'approved', 'score' => 90])->assertForbidden();

        $this->assertSame('pending', $this->submission->fresh()->status);
        $this->assertDatabaseMissing('lesson_completions', ['lesson_id' => $this->l1->id]);
    }

    public function test_approving_completes_lesson_updates_progress_and_unlocks_next(): void
    {
        $this->actingAs($this->mentor);
        $this->review($this->submission, ['decision' => 'approved', 'score' => 90, 'mentor_feedback' => 'Mantap!'])
            ->assertRedirect(route('mentor.submissions.index'))->assertSessionHas('status');

        $fresh = $this->submission->fresh();
        $this->assertSame('approved', $fresh->status);
        $this->assertSame(90, $fresh->score);
        $this->assertSame($this->mentor->id, $fresh->reviewer_id);
        $this->assertNotNull($fresh->reviewed_at);

        $this->assertDatabaseHas('lesson_completions', ['student_id' => $this->student->id, 'lesson_id' => $this->l1->id]);
        $this->assertSame(50, $this->enrollment->fresh()->progress_percent);

        $this->actingAs($this->student)->get(route('learn.show', [$this->course, $this->l2]))->assertOk();
    }

    public function test_approving_requires_score_at_or_above_passing_score(): void
    {
        $this->actingAs($this->mentor);
        $this->review($this->submission, ['decision' => 'approved', 'score' => 74])->assertSessionHasErrors('score');

        $this->assertSame('pending', $this->submission->fresh()->status);
        $this->assertDatabaseMissing('lesson_completions', ['lesson_id' => $this->l1->id]);

        $this->review($this->submission, ['decision' => 'approved', 'score' => 75])->assertSessionHasNoErrors();
        $this->assertSame('approved', $this->submission->fresh()->status);
    }

    public function test_rejecting_requires_feedback_and_a_failing_score(): void
    {
        $this->actingAs($this->mentor);

        $this->review($this->submission, ['decision' => 'rejected', 'score' => 40])->assertSessionHasErrors('mentor_feedback');
        $this->review($this->submission, ['decision' => 'rejected', 'score' => 90, 'mentor_feedback' => 'Kurang'])->assertSessionHasErrors('score');
        $this->assertSame('pending', $this->submission->fresh()->status);
    }

    public function test_rejecting_keeps_lesson_locked_and_does_not_complete_it(): void
    {
        $this->actingAs($this->mentor);
        $this->review($this->submission, ['decision' => 'rejected', 'score' => 40, 'mentor_feedback' => 'Perbaiki struktur folder.'])
            ->assertRedirect(route('mentor.submissions.index'));

        $fresh = $this->submission->fresh();
        $this->assertSame('rejected', $fresh->status);
        $this->assertSame('Perbaiki struktur folder.', $fresh->mentor_feedback);
        $this->assertDatabaseMissing('lesson_completions', ['lesson_id' => $this->l1->id]);
        $this->assertSame(0, $this->enrollment->fresh()->progress_percent);

        $this->actingAs($this->student)->get(route('learn.show', [$this->course, $this->l2]))
            ->assertRedirect(route('learn.show', [$this->course, $this->l1]));
    }

    public function test_reject_then_resubmit_then_approve_keeps_full_history(): void
    {
        $this->actingAs($this->mentor);
        $this->review($this->submission, ['decision' => 'rejected', 'score' => 40, 'mentor_feedback' => 'Belum lengkap.']);

        $this->actingAs($this->student)->post(route('student.submissions.store', [$this->course, $this->l1]), [
            'attachment_url' => 'https://github.com/budi/revisi',
        ])->assertSessionHasNoErrors();

        $second = Submission::latest('id')->first();
        $this->assertNotSame($this->submission->id, $second->id);
        $this->assertDatabaseCount('submissions', 2);

        $this->actingAs($this->mentor);
        $this->review($second, ['decision' => 'approved', 'score' => 88]);

        $this->assertSame('rejected', $this->submission->fresh()->status);
        $this->assertSame('Belum lengkap.', $this->submission->fresh()->mentor_feedback);
        $this->assertSame('approved', $second->fresh()->status);
        $this->assertSame(50, $this->enrollment->fresh()->progress_percent);

        $this->get(route('mentor.submissions.show', $second))->assertOk()->assertSee('Riwayat pengiriman siswa ini')->assertSee('Belum lengkap.');
    }

    public function test_a_submission_cannot_be_reviewed_twice(): void
    {
        $this->actingAs($this->mentor);
        $this->review($this->submission, ['decision' => 'approved', 'score' => 90]);

        $this->review($this->submission, ['decision' => 'rejected', 'score' => 10, 'mentor_feedback' => 'Ralat'])
            ->assertSessionHasErrors('decision');

        $this->assertSame('approved', $this->submission->fresh()->status);
        $this->assertSame(90, $this->submission->fresh()->score);
    }

    public function test_approving_the_last_lesson_completes_course_and_issues_certificate(): void
    {
        $this->actingAs($this->mentor);
        $this->review($this->submission, ['decision' => 'approved', 'score' => 90]);

        $second = Submission::factory()->create([
            'assignment_id' => $this->l2->assignment->id, 'student_id' => $this->student->id,
        ]);
        $this->review($second, ['decision' => 'approved', 'score' => 95]);

        $enrollment = $this->enrollment->fresh();
        $this->assertSame(100, $enrollment->progress_percent);
        $this->assertNotNull($enrollment->completed_at);
        $this->assertMatchesRegularExpression('/^CRYGLE-CERT-[A-Z0-9]{10}$/', $enrollment->certificate_code);
    }

    public function test_dashboard_pending_card_links_to_review_queue(): void
    {
        $this->actingAs($this->mentor)->get(route('mentor.dashboard'))
            ->assertOk()->assertSee('Menunggu Review')->assertSee(route('mentor.submissions.index'), false);
    }
}
