<?php

namespace Tests\Feature\Student;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Support\VideoEmbed;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePlayerTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private Course $course;
    /** @var array<int, Lesson> urutan belajar yang benar: L1, L2, L3(tugas), L4 */
    private array $lessons;
    private Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $mentor = tap(User::factory()->create(), fn ($u) => $u->assignRole('mentor'));
        $this->student = tap(User::factory()->create(), fn ($u) => $u->assignRole('student'));
        $this->course = Course::factory()->create(['mentor_id' => $mentor->id, 'price' => 0]);

        // Sengaja: modul urutan 2 dibuat lebih dulu (id kecil) untuk menguji pengurutan by sort_order.
        $m2 = Module::factory()->create(['course_id' => $this->course->id, 'sort_order' => 2]);
        $m1 = Module::factory()->create(['course_id' => $this->course->id, 'sort_order' => 1]);

        $l1 = Lesson::factory()->create(['module_id' => $m1->id, 'sort_order' => 1, 'title' => 'L1']);
        $l2 = Lesson::factory()->create(['module_id' => $m1->id, 'sort_order' => 2, 'title' => 'L2']);
        $l3 = Lesson::factory()->create(['module_id' => $m2->id, 'sort_order' => 1, 'title' => 'L3']);
        $l4 = Lesson::factory()->create(['module_id' => $m2->id, 'sort_order' => 2, 'title' => 'L4']);
        Assignment::factory()->create(['lesson_id' => $l3->id]);
        $this->lessons = [$l1, $l2, $l3, $l4];

        $this->enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id, 'course_id' => $this->course->id, 'payment_status' => 'paid',
        ]);
    }

    private function show(Lesson $l) { return route('learn.show', [$this->course, $l]); }
    private function complete(Lesson $l) { return route('learn.complete', [$this->course, $l]); }

    public function test_index_redirects_to_first_lesson_in_sort_order(): void
    {
        $this->actingAs($this->student)->get(route('learn.index', $this->course))->assertRedirect($this->show($this->lessons[0]));
    }

    public function test_locked_lesson_redirects_to_current_lesson(): void
    {
        $this->actingAs($this->student)->get($this->show($this->lessons[1]))
            ->assertRedirect($this->show($this->lessons[0]))->assertSessionHasErrors('lesson');
    }

    public function test_completing_lessons_in_order_unlocks_next_and_updates_progress(): void
    {
        $this->actingAs($this->student);
        $this->get($this->show($this->lessons[0]))->assertOk()->assertSee('L1');

        $this->post($this->complete($this->lessons[0]))->assertRedirect($this->show($this->lessons[1]));
        $this->assertSame(25, $this->enrollment->fresh()->progress_percent);

        $this->get($this->show($this->lessons[1]))->assertOk();
        $this->post($this->complete($this->lessons[1]))->assertRedirect($this->show($this->lessons[2]));
        $this->assertSame(50, $this->enrollment->fresh()->progress_percent);
        $this->assertNull($this->enrollment->fresh()->completed_at);
    }

    public function test_cannot_complete_locked_lesson_or_skip_ahead(): void
    {
        $this->actingAs($this->student)->post($this->complete($this->lessons[1]))->assertSessionHasErrors('lesson');
        $this->assertDatabaseCount('lesson_completions', 0);
    }

    public function test_lesson_with_assignment_cannot_be_completed_manually(): void
    {
        $this->actingAs($this->student);
        $this->post($this->complete($this->lessons[0]));
        $this->post($this->complete($this->lessons[1]));

        $this->get($this->show($this->lessons[2]))->assertOk()->assertSee('Tugas:');
        $this->post($this->complete($this->lessons[2]))->assertSessionHasErrors('lesson');
        $this->assertDatabaseMissing('lesson_completions', ['lesson_id' => $this->lessons[2]->id]);
        // L4 tetap terkunci
        $this->get($this->show($this->lessons[3]))->assertRedirect($this->show($this->lessons[2]));
    }

    public function test_completion_is_idempotent(): void
    {
        $this->actingAs($this->student);
        $this->post($this->complete($this->lessons[0]));
        $this->post($this->complete($this->lessons[0]));

        $this->assertDatabaseCount('lesson_completions', 1);
        $this->assertSame(25, $this->enrollment->fresh()->progress_percent);
    }

    public function test_reaching_100_percent_sets_completed_at_and_unique_certificate_code(): void
    {
        $service = app(CourseProgressService::class);
        foreach ($this->lessons as $lesson) {
            $service->markCompleted($this->student, $lesson, $this->enrollment->fresh());
        }

        $enrollment = $this->enrollment->fresh();
        $this->assertSame(100, $enrollment->progress_percent);
        $this->assertNotNull($enrollment->completed_at);
        $this->assertMatchesRegularExpression('/^CRYGLE-CERT-[A-Z0-9]{10}$/', $enrollment->certificate_code);

        $code = $enrollment->certificate_code;
        $service->recalculate($enrollment->fresh());
        $this->assertSame($code, $enrollment->fresh()->certificate_code);
    }

    public function test_pending_or_missing_enrollment_gets_no_access(): void
    {
        $this->enrollment->update(['payment_status' => 'pending']);
        $this->actingAs($this->student)->get($this->show($this->lessons[0]))->assertRedirect(route('catalog.show', $this->course));

        $stranger = tap(User::factory()->create(), fn ($u) => $u->assignRole('student'));
        $this->actingAs($stranger)->get($this->show($this->lessons[0]))->assertRedirect(route('catalog.show', $this->course));
        $this->actingAs($stranger)->post($this->complete($this->lessons[0]))->assertForbidden();
    }

    public function test_free_preview_lesson_is_viewable_without_enrollment_but_not_completable(): void
    {
        $this->lessons[2]->update(['is_free_preview' => true]);
        $stranger = tap(User::factory()->create(), fn ($u) => $u->assignRole('student'));

        $this->actingAs($stranger)->get($this->show($this->lessons[2]))->assertOk()->assertSee('Preview gratis');
        $this->actingAs($stranger)->get($this->show($this->lessons[3]))->assertRedirect(route('catalog.show', $this->course));
        $this->actingAs($stranger)->post($this->complete($this->lessons[2]))->assertForbidden();
    }

    public function test_mentor_owner_can_preview_any_lesson_but_other_mentor_cannot(): void
    {
        $this->actingAs($this->course->mentor)->get($this->show($this->lessons[3]))->assertOk()->assertSee('Pratinjau mentor');

        $other = tap(User::factory()->create(), fn ($u) => $u->assignRole('mentor'));
        $this->actingAs($other)->get($this->show($this->lessons[3]))->assertRedirect(route('catalog.show', $this->course));
    }

    public function test_lesson_from_another_course_is_404(): void
    {
        $foreign = Lesson::factory()->create();

        $this->actingAs($this->student)->get($this->show($foreign))->assertNotFound();
    }

    public function test_markdown_content_is_rendered_and_html_is_stripped(): void
    {
        $this->lessons[0]->update(['content_text' => "# Judul Materi\n\n<script>alert(1)</script>\n\n```php\necho 1;\n```\n\n[x](javascript:alert(1))"]);

        $this->actingAs($this->student)->get($this->show($this->lessons[0]))->assertOk()
            ->assertSee('<h1>Judul Materi</h1>', false)
            ->assertSee('language-php', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('href="javascript:', false);
    }

    public function test_video_embed_parsing(): void
    {
        $this->assertSame(['provider' => 'youtube', 'id' => 'dQw4w9WgXcQ'], VideoEmbed::parse('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $this->assertSame(['provider' => 'youtube', 'id' => 'dQw4w9WgXcQ'], VideoEmbed::parse('https://youtu.be/dQw4w9WgXcQ?t=3'));
        $this->assertSame(['provider' => 'vimeo', 'id' => '12345'], VideoEmbed::parse('https://vimeo.com/12345'));
        $this->assertSame(['provider' => 'file', 'url' => 'https://x.test/a.mp4'], VideoEmbed::parse('https://x.test/a.mp4'));
        $this->assertNull(VideoEmbed::parse('https://example.com/page'));
        $this->assertNull(VideoEmbed::parse(null));
    }

    public function test_my_courses_links_to_player(): void
    {
        $this->actingAs($this->student)->get(route('student.courses'))->assertOk()
            ->assertSee(route('learn.index', $this->course), false)->assertSee('Mulai Belajar');
    }
}
