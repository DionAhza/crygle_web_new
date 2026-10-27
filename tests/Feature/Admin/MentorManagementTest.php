<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\MentorType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MentorManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function user(string $role): User
    {
        return tap(User::factory()->create(), fn ($u) => $u->assignRole($role));
    }

    public function test_only_admin_can_access_admin_area(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        foreach (['mentor', 'student'] as $role) {
            $this->actingAs($this->user($role));
            foreach (['/admin', '/admin/mentors', '/admin/mentors/create', '/admin/mentor-types'] as $url) {
                $this->get($url)->assertForbidden();
            }
            $this->post('/admin/mentor-types', ['name' => 'X'])->assertForbidden();
            $this->post('/admin/mentors', [])->assertForbidden();
        }

        $this->actingAs($this->user('admin'))->get('/admin')->assertOk()->assertSee('Dashboard Admin');
    }

    public function test_admin_is_redirected_from_dashboard_to_admin_dashboard(): void
    {
        $this->actingAs($this->user('admin'))->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_crud_mentor_types_with_unique_slug(): void
    {
        $this->actingAs($this->user('admin'));

        $this->post(route('admin.mentor-types.store'), ['name' => 'UI/UX'])->assertSessionHasNoErrors();
        $this->post(route('admin.mentor-types.store'), ['name' => '3D Modeling']);
        $this->assertEqualsCanonicalizing(['uiux', '3d-modeling'], MentorType::pluck('slug')->all());

        // nama ganda ditolak; slug bentrok diberi sufiks
        $this->post(route('admin.mentor-types.store'), ['name' => 'UI/UX'])->assertSessionHasErrors('name');
        $this->post(route('admin.mentor-types.store'), ['name' => 'Ui-Ux']);
        $this->assertContains('ui-ux', MentorType::pluck('slug')->all());
        $this->post(route('admin.mentor-types.store'), ['name' => 'UIUX!']);
        $this->assertContains('uiux-2', MentorType::pluck('slug')->all());

        $type = MentorType::where('slug', '3d-modeling')->first();
        $this->patch(route('admin.mentor-types.update', $type), ['name' => 'Animasi 3D'])->assertSessionHasNoErrors();
        $this->assertSame('animasi-3d', $type->fresh()->slug);
        // menyimpan nama yang sama untuk dirinya sendiri tidak dianggap duplikat
        $this->patch(route('admin.mentor-types.update', $type), ['name' => 'Animasi 3D'])->assertSessionHasNoErrors();

        $this->delete(route('admin.mentor-types.destroy', $type))->assertSessionHasNoErrors();
        $this->assertModelMissing($type);
        $this->get(route('admin.mentor-types.index'))->assertOk()->assertSee('UI/UX');
    }

    public function test_type_in_use_cannot_be_deleted(): void
    {
        $type = MentorType::factory()->create();
        User::factory()->create(['mentor_type_id' => $type->id])->assignRole('mentor');

        $this->actingAs($this->user('admin'))->delete(route('admin.mentor-types.destroy', $type))->assertSessionHasErrors('mentor_type');
        $this->assertModelExists($type);
    }

    public function test_admin_creates_mentor_with_type_role_and_verified_email(): void
    {
        $type = MentorType::factory()->create(['name' => 'WebDev']);
        $this->actingAs($this->user('admin'));

        $this->post(route('admin.mentors.store'), [
            'name' => 'Mentor Baru', 'email' => 'Baru@Example.com', 'mentor_type_id' => $type->id,
            'password' => 'rahasia-123', 'password_confirmation' => 'rahasia-123',
        ])->assertRedirect(route('admin.mentors.index'))->assertSessionHasNoErrors();

        $mentor = User::where('email', 'baru@example.com')->firstOrFail();
        $this->assertTrue($mentor->hasRole('mentor'));
        $this->assertFalse($mentor->hasRole('student'));
        $this->assertSame($type->id, $mentor->mentor_type_id);
        $this->assertNotNull($mentor->email_verified_at);
        $this->assertTrue(Hash::check('rahasia-123', $mentor->password));

        // mentor baru bisa langsung login dan masuk dashboard mentor
        auth()->logout();
        $this->post('/login', ['email' => 'baru@example.com', 'password' => 'rahasia-123']);
        $this->get('/dashboard')->assertRedirect(route('mentor.dashboard'));
        $this->get('/mentor')->assertOk();
    }

    public function test_mentor_creation_validation(): void
    {
        $this->actingAs($this->user('admin'));
        $existing = $this->user('student');

        $this->post(route('admin.mentors.store'), [])->assertSessionHasErrors(['name', 'email', 'mentor_type_id', 'password']);
        $this->post(route('admin.mentors.store'), [
            'name' => 'X', 'email' => $existing->email, 'mentor_type_id' => 9999,
            'password' => 'rahasia-123', 'password_confirmation' => 'beda',
        ])->assertSessionHasErrors(['email', 'mentor_type_id', 'password']);

        $this->assertSame(0, User::role('mentor')->count());
    }

    public function test_admin_edits_mentor_with_optional_password(): void
    {
        $admin = $this->user('admin');
        $a = MentorType::factory()->create();
        $b = MentorType::factory()->create();
        $mentor = $this->user('mentor');
        $mentor->update(['mentor_type_id' => $a->id, 'password' => 'lama-12345']);

        $this->actingAs($admin)->put(route('admin.mentors.update', $mentor), [
            'name' => 'Nama Baru', 'email' => $mentor->email, 'mentor_type_id' => $b->id,
        ])->assertSessionHasNoErrors();

        $mentor->refresh();
        $this->assertSame('Nama Baru', $mentor->name);
        $this->assertSame($b->id, $mentor->mentor_type_id);
        $this->assertTrue(Hash::check('lama-12345', $mentor->password));

        $this->put(route('admin.mentors.update', $mentor), [
            'name' => 'Nama Baru', 'email' => $mentor->email, 'mentor_type_id' => $b->id,
            'password' => 'baru-123456', 'password_confirmation' => 'baru-123456',
        ])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('baru-123456', $mentor->fresh()->password));
    }

    public function test_admin_cannot_edit_or_delete_non_mentor_via_mentor_routes(): void
    {
        $student = $this->user('student');
        $this->actingAs($this->user('admin'));

        $this->get(route('admin.mentors.edit', $student))->assertNotFound();
        $this->delete(route('admin.mentors.destroy', $student))->assertNotFound();
        $this->assertModelExists($student);
    }

    public function test_mentor_with_courses_cannot_be_deleted_but_empty_one_can(): void
    {
        $this->actingAs($this->user('admin'));
        $busy = $this->user('mentor');
        Course::factory()->create(['mentor_id' => $busy->id]);
        $idle = $this->user('mentor');

        $this->delete(route('admin.mentors.destroy', $busy))->assertSessionHasErrors('mentor');
        $this->assertModelExists($busy);

        $this->delete(route('admin.mentors.destroy', $idle))->assertSessionHasNoErrors();
        $this->assertModelMissing($idle);
    }

    public function test_mentor_list_filters_by_search_and_type(): void
    {
        $this->actingAs($this->user('admin'));
        $web = MentorType::factory()->create(['name' => 'WebDev']);
        $three = MentorType::factory()->create(['name' => 'Tiga D']);
        $m1 = $this->user('mentor'); $m1->update(['name' => 'Budi Web', 'mentor_type_id' => $web->id]);
        $m2 = $this->user('mentor'); $m2->update(['name' => 'Sari Model', 'mentor_type_id' => $three->id]);

        $this->get(route('admin.mentors.index'))->assertSee('Budi Web')->assertSee('Sari Model');
        $this->get(route('admin.mentors.index', ['type' => $web->id]))->assertSee('Budi Web')->assertDontSee('Sari Model');
        $this->get(route('admin.mentors.index', ['q' => 'Sari']))->assertSee('Sari Model')->assertDontSee('Budi Web');
    }

    public function test_public_registration_never_creates_a_mentor(): void
    {
        $this->post('/register', [
            'name' => 'Pendaftar', 'email' => 'p@example.com', 'password' => 'password-123',
            'password_confirmation' => 'password-123', 'terms' => '1',
            'role' => 'mentor', 'mentor_type_id' => 1,
        ]);

        $user = User::where('email', 'p@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('student'));
        $this->assertFalse($user->hasRole('mentor'));
        $this->assertNull($user->mentor_type_id);

        auth()->logout();
        $this->get('/register')->assertOk()->assertDontSee('name="role"', false);
    }

    public function test_catalog_shows_mentor_type_badge(): void
    {
        $type = MentorType::factory()->create(['name' => 'Spesialis UIUX']);
        $mentor = $this->user('mentor');
        $mentor->update(['mentor_type_id' => $type->id]);
        $course = Course::factory()->create(['mentor_id' => $mentor->id, 'is_published' => true]);

        $this->get(route('catalog.show', $course))->assertOk()->assertSee('Spesialis UIUX');
    }
}
