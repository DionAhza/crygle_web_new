<?php

namespace Tests\Feature\Mentor;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MentorCourseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function mentor(): User
    {
        return tap(User::factory()->create(), fn ($u) => $u->assignRole('mentor'));
    }

    public function test_guest_is_redirected_and_student_is_forbidden(): void
    {
        $this->get('/mentor')->assertRedirect('/login');

        $student = User::factory()->create();
        $student->assignRole('student');
        $this->actingAs($student)->get('/mentor')->assertForbidden();
        $this->actingAs($student)->get('/mentor/courses')->assertForbidden();
    }

    public function test_mentor_dashboard_and_dashboard_redirect(): void
    {
        $mentor = $this->mentor();

        $this->actingAs($mentor)->get('/dashboard')->assertRedirect(route('mentor.dashboard'));
        $this->actingAs($mentor)->get('/mentor')->assertOk()->assertSee('Dashboard Mentor');
    }

    public function test_mentor_can_create_course_with_thumbnail(): void
    {
        Storage::fake('public');
        $mentor = $this->mentor();

        $response = $this->actingAs($mentor)->post('/mentor/courses', [
            'title' => 'Belajar Laravel',
            'description' => 'Deskripsi',
            'price' => 150000,
            'level' => 'basic',
            'is_published' => '1',
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
        ]);

        $course = Course::firstOrFail();
        $response->assertRedirect(route('mentor.courses.show', $course));
        $this->assertSame($mentor->id, $course->mentor_id);
        $this->assertSame('belajar-laravel', $course->slug);
        $this->assertTrue($course->is_published);
        $this->assertNotNull($course->thumbnail_url);
    }

    public function test_slug_is_unique_for_duplicate_titles(): void
    {
        $mentor = $this->mentor();
        $payload = ['title' => 'Kelas Sama', 'price' => 0, 'level' => 'basic'];

        $this->actingAs($mentor)->post('/mentor/courses', $payload);
        $this->actingAs($mentor)->post('/mentor/courses', $payload);

        $this->assertEqualsCanonicalizing(['kelas-sama', 'kelas-sama-2'], Course::pluck('slug')->all());
    }

    public function test_validation_rejects_invalid_course(): void
    {
        $this->actingAs($this->mentor())
            ->post('/mentor/courses', ['title' => '', 'price' => -5, 'level' => 'expert'])
            ->assertSessionHasErrors(['title', 'price', 'level']);
    }

    public function test_mentor_cannot_access_another_mentors_course(): void
    {
        $owner = $this->mentor();
        $other = $this->mentor();
        $course = Course::factory()->create(['mentor_id' => $owner->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['module_id' => $module->id]);
        $assignment = Assignment::factory()->create(['lesson_id' => $lesson->id]);

        $this->actingAs($other);
        $this->get(route('mentor.courses.show', $course))->assertForbidden();
        $this->get(route('mentor.courses.edit', $course))->assertForbidden();
        $this->put(route('mentor.courses.update', $course), ['title' => 'x', 'price' => 0, 'level' => 'basic'])->assertForbidden();
        $this->delete(route('mentor.courses.destroy', $course))->assertForbidden();
        $this->post(route('mentor.modules.store', $course), ['title' => 'Bab'])->assertForbidden();
        $this->patch(route('mentor.modules.update', $module), ['title' => 'x'])->assertForbidden();
        $this->get(route('mentor.lessons.create', $module))->assertForbidden();
        $this->get(route('mentor.lessons.edit', $lesson))->assertForbidden();
        $this->delete(route('mentor.lessons.destroy', $lesson))->assertForbidden();
        $this->get(route('mentor.assignments.edit', $assignment))->assertForbidden();
        $this->delete(route('mentor.assignments.destroy', $assignment))->assertForbidden();

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => $course->title]);
    }

    public function test_mentor_builds_curriculum_with_auto_sort_order(): void
    {
        $mentor = $this->mentor();
        $course = Course::factory()->create(['mentor_id' => $mentor->id]);
        $this->actingAs($mentor);

        $this->post(route('mentor.modules.store', $course), ['title' => 'Bab 1'])->assertSessionHasNoErrors();
        $this->post(route('mentor.modules.store', $course), ['title' => 'Bab 2']);
        $this->assertSame([1, 2], $course->modules()->pluck('sort_order')->all());

        $module = $course->modules()->first();
        $this->post(route('mentor.lessons.store', $module), ['title' => 'Materi A', 'video_url' => 'https://youtu.be/abc']);
        $this->post(route('mentor.lessons.store', $module), ['title' => 'Materi A']);
        $this->assertSame([1, 2], $module->lessons()->pluck('sort_order')->all());
        $this->assertEqualsCanonicalizing(['materi-a', 'materi-a-2'], $module->lessons()->pluck('slug')->all());

        $lesson = $module->lessons()->first();
        $this->post(route('mentor.assignments.store', $lesson), [
            'title' => 'Tugas 1', 'submission_type' => 'repo_link', 'passing_score' => 80,
        ])->assertSessionHasNoErrors();
        $this->assertSame(80, $lesson->assignment->passing_score);

        $this->get(route('mentor.courses.show', $course))->assertOk()->assertSee('Materi A')->assertSee('Tugas 1');
        $this->get(route('mentor.assignments.create', $lesson))->assertRedirect(route('mentor.assignments.edit', $lesson->assignment));
    }

    public function test_course_with_enrollments_cannot_be_deleted(): void
    {
        $mentor = $this->mentor();
        $course = Course::factory()->create(['mentor_id' => $mentor->id]);
        Enrollment::factory()->create(['course_id' => $course->id]);

        $this->actingAs($mentor)->delete(route('mentor.courses.destroy', $course))->assertSessionHasErrors('course');
        $this->assertModelExists($course);
    }

    public function test_empty_course_can_be_deleted(): void
    {
        $mentor = $this->mentor();
        $course = Course::factory()->create(['mentor_id' => $mentor->id]);

        $this->actingAs($mentor)->delete(route('mentor.courses.destroy', $course))->assertRedirect(route('mentor.courses.index'));
        $this->assertModelMissing($course);
    }

    public function test_pages_render(): void
    {
        $mentor = $this->mentor();
        $course = Course::factory()->create(['mentor_id' => $mentor->id]);
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['module_id' => $module->id]);
        $assignment = Assignment::factory()->create(['lesson_id' => $lesson->id]);
        $this->actingAs($mentor);

        foreach ([
            route('mentor.courses.index'), route('mentor.courses.create'), route('mentor.courses.edit', $course),
            route('mentor.lessons.create', $module), route('mentor.lessons.edit', $lesson),
            route('mentor.assignments.edit', $assignment),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
