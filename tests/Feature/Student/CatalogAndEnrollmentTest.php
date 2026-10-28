<?php

namespace Tests\Feature\Student;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Services\MidtransService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAndEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        config(['services.midtrans.server_key' => 'SB-Mid-server-test', 'services.midtrans.client_key' => 'SB-Mid-client-test']);
    }

    private function user(string $role): User
    {
        return tap(User::factory()->create(), fn ($u) => $u->assignRole($role));
    }

    private function course(array $attrs = []): Course
    {
        return Course::factory()->create(array_merge(['mentor_id' => $this->user('mentor')->id], $attrs));
    }

    public function test_catalog_lists_only_published_courses_and_is_public(): void
    {
        $this->course(['title' => 'Kelas Terbit', 'is_published' => true]);
        $this->course(['title' => 'Kelas Draft', 'is_published' => false]);

        $this->get('/kelas')->assertOk()->assertSee('Kelas Terbit')->assertDontSee('Kelas Draft');
    }

    public function test_catalog_filters_by_search_level_and_price(): void
    {
        $this->course(['title' => 'Laravel Dasar', 'level' => 'basic', 'price' => 0]);
        $this->course(['title' => 'Laravel Mahir', 'level' => 'advanced', 'price' => 200000]);
        $this->course(['title' => 'Desain Grafis', 'level' => 'basic', 'price' => 100000]);

        $this->get('/kelas?q=Laravel')->assertSee('Laravel Dasar')->assertSee('Laravel Mahir')->assertDontSee('Desain Grafis');
        $this->get('/kelas?level=advanced')->assertSee('Laravel Mahir')->assertDontSee('Laravel Dasar');
        $this->get('/kelas?price=free')->assertSee('Laravel Dasar')->assertDontSee('Desain Grafis');
        $this->get('/kelas?level=bogus')->assertSessionHasErrors('level');
    }

    public function test_course_detail_shows_syllabus(): void
    {
        $course = $this->course(['title' => 'Kelas Silabus']);
        $module = Module::factory()->create(['course_id' => $course->id, 'title' => 'Bab Pengenalan']);
        Lesson::factory()->create(['module_id' => $module->id, 'title' => 'Materi Pertama', 'is_free_preview' => true]);

        $this->get(route('catalog.show', $course))->assertOk()
            ->assertSee('Bab Pengenalan')->assertSee('Materi Pertama')->assertSee('Preview')
            ->assertSee('Masuk untuk Mendaftar');
    }

    public function test_draft_course_is_404_except_for_owner(): void
    {
        $course = $this->course(['is_published' => false]);

        $this->get(route('catalog.show', $course))->assertNotFound();
        $this->actingAs($this->user('student'))->get(route('catalog.show', $course))->assertNotFound();
        $this->actingAs($course->mentor)->get(route('catalog.show', $course))->assertOk();
    }

    public function test_registration_assigns_student_role(): void
    {
        $this->post('/register', [
            'name' => 'Siswa Baru', 'email' => 'baru@example.com',
            'password' => 'password-123', 'password_confirmation' => 'password-123', 'terms' => '1',
        ]);

        $this->assertTrue(User::where('email', 'baru@example.com')->first()->hasRole('student'));
    }

    public function test_student_enrolls_free_course_instantly_and_idempotently(): void
    {
        $student = $this->user('student');
        $course = $this->course(['price' => 0]);

        $this->actingAs($student)->post(route('student.enroll', $course))->assertRedirect(route('student.courses'));
        $this->actingAs($student)->post(route('student.enroll', $course));

        $this->assertDatabaseCount('enrollments', 1);
        $this->assertDatabaseHas('enrollments', ['student_id' => $student->id, 'course_id' => $course->id, 'payment_status' => 'paid']);
        $this->actingAs($student)->get(route('student.courses'))->assertOk()->assertSee($course->title);
    }

    public function test_paid_course_creates_pending_enrollment_and_redirects_to_checkout(): void
    {
        $student = $this->user('student');
        $course = $this->course(['price' => 150000]);

        $response = $this->actingAs($student)->post(route('student.enroll', $course));

        $enrollment = Enrollment::firstOrFail();
        $response->assertRedirect(route('student.checkout', $enrollment));
        $this->assertSame('pending', $enrollment->payment_status);
    }

    public function test_checkout_shows_snap_button_when_token_available(): void
    {
        $this->mock(MidtransService::class, function ($mock) {
            $mock->shouldReceive('configured')->andReturn(true);
            $mock->shouldReceive('syncEnrollment')->andReturn(false);
            $mock->shouldReceive('createSnapToken')->once()->andReturn('snap-token-123');
            $mock->shouldReceive('snapJsUrl')->andReturn('https://app.sandbox.midtrans.com/snap/snap.js');
            $mock->shouldReceive('clientKey')->andReturn('client-key');
        });

        $enrollment = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 99000])->id, 'student_id' => $this->user('student')->id]);

        $this->actingAs($enrollment->student)->get(route('student.checkout', $enrollment))
            ->assertOk()->assertSee('snap-token-123')->assertSee('Rp 99.000');
    }

    public function test_student_cannot_open_someone_elses_checkout(): void
    {
        $enrollment = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 99000])->id, 'student_id' => $this->user('student')->id]);

        $this->actingAs($this->user('student'))->get(route('student.checkout', $enrollment))->assertForbidden();
    }

    public function test_non_students_cannot_enroll(): void
    {
        $course = $this->course();

        $this->actingAs($this->user('mentor'))->post(route('student.enroll', $course))->assertForbidden();
    }

    public function test_guests_cannot_enroll(): void
    {
        $this->post(route('student.enroll', $this->course()))->assertRedirect('/login');
    }

    public function test_simulate_is_unavailable_outside_local(): void
    {
        $enrollment = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 99000])->id, 'student_id' => $this->user('student')->id]);

        $this->actingAs($enrollment->student)->post(route('student.checkout.simulate', $enrollment))->assertNotFound();
        $this->assertSame('pending', $enrollment->fresh()->payment_status);
    }

    private function notification(Enrollment $enrollment, array $override = []): array
    {
        $payload = array_merge([
            'order_id' => 'ENR-'.$enrollment->id.'-1700000000',
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'transaction_status' => 'settlement',
        ], $override);
        $payload['signature_key'] = $override['signature_key']
            ?? hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'SB-Mid-server-test');

        return $payload;
    }

    public function test_webhook_marks_enrollment_paid_with_valid_signature(): void
    {
        $enrollment = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 150000])->id]);

        $this->postJson('/midtrans/notification', $this->notification($enrollment))->assertOk();

        $this->assertSame('paid', $enrollment->fresh()->payment_status);
    }

    public function test_webhook_rejects_bad_signature_and_wrong_amount(): void
    {
        $enrollment = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 150000])->id]);

        $this->postJson('/midtrans/notification', $this->notification($enrollment, ['signature_key' => 'salah']))->assertForbidden();
        $this->postJson('/midtrans/notification', $this->notification($enrollment, ['gross_amount' => '1000.00']))->assertStatus(422);

        $this->assertSame('pending', $enrollment->fresh()->payment_status);
    }

    public function test_webhook_failed_status_and_never_downgrades_paid(): void
    {
        $pending = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 150000])->id]);
        $this->postJson('/midtrans/notification', $this->notification($pending, ['transaction_status' => 'expire']))->assertOk();
        $this->assertSame('failed', $pending->fresh()->payment_status);

        $paid = Enrollment::factory()->create(['payment_status' => 'paid', 'course_id' => $this->course(['price' => 150000])->id]);
        $this->postJson('/midtrans/notification', $this->notification($paid, ['transaction_status' => 'expire']))->assertOk();
        $this->assertSame('paid', $paid->fresh()->payment_status);
    }

    private function fakeMidtrans(array $status): void
    {
        config(['services.midtrans.server_key' => 'SB-key', 'services.midtrans.client_key' => 'SB-client']);
        $this->app->bind(\App\Services\MidtransService::class, fn () => new class($status) extends \App\Services\MidtransService {
            public function __construct(private array $status) {}

            public function fetchStatus(string $orderId): array
            {
                return $this->status;
            }
        });
    }

    public function test_finish_activates_enrollment_when_midtrans_reports_settlement(): void
    {
        $enrollment = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 150000])->id, 'student_id' => $this->user('student')->id]);
        $orderId = 'ENR-'.$enrollment->id.'-1700000000';
        $this->fakeMidtrans(['order_id' => $orderId, 'gross_amount' => '150000.00', 'transaction_status' => 'settlement']);

        $this->actingAs($enrollment->student)
            ->get(route('student.checkout.finish', $enrollment).'?order_id='.$orderId)
            ->assertRedirect(route('student.checkout.success', $enrollment));

        $this->assertSame('paid', $enrollment->fresh()->payment_status);
    }

    public function test_finish_ignores_order_id_of_another_enrollment_and_pending_status(): void
    {
        $enrollment = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 150000])->id, 'student_id' => $this->user('student')->id]);
        $this->fakeMidtrans(['order_id' => 'ENR-999-1', 'gross_amount' => '150000.00', 'transaction_status' => 'settlement']);

        $this->actingAs($enrollment->student)->get(route('student.checkout.finish', $enrollment).'?order_id=ENR-999-1')
            ->assertOk()->assertSee('Memproses pembayaran kamu');
        $this->assertSame('pending', $enrollment->fresh()->payment_status);

        $orderId = 'ENR-'.$enrollment->id.'-1';
        $this->fakeMidtrans(['order_id' => $orderId, 'gross_amount' => '150000.00', 'transaction_status' => 'pending']);
        $this->actingAs($enrollment->student)->get(route('student.checkout.finish', $enrollment).'?order_id='.$orderId);
        $this->assertSame('pending', $enrollment->fresh()->payment_status);
    }

    public function test_status_endpoint_reports_paid_and_failed(): void
    {
        $enrollment = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $this->course(['price' => 150000])->id, 'student_id' => $this->user('student')->id, 'midtrans_order_id' => 'ENR-0-1']);
        $orderId = 'ENR-'.$enrollment->id.'-1';
        $enrollment->update(['midtrans_order_id' => $orderId]);

        $this->fakeMidtrans(['order_id' => $orderId, 'gross_amount' => '150000.00', 'transaction_status' => 'expire']);
        $this->actingAs($enrollment->student)->getJson(route('student.checkout.status', $enrollment))
            ->assertOk()->assertJson(['paid' => false, 'status' => 'failed']);

        $enrollment->update(['payment_status' => 'pending']);
        $this->fakeMidtrans(['order_id' => $orderId, 'gross_amount' => '150000.00', 'transaction_status' => 'settlement']);
        $this->actingAs($enrollment->student)->getJson(route('student.checkout.status', $enrollment))
            ->assertOk()->assertJson(['paid' => true, 'status' => 'paid']);
    }

    public function test_success_and_receipt_pages_only_for_paid_owner(): void
    {
        $course = $this->course(['price' => 150000]);
        $paid = Enrollment::factory()->create(['payment_status' => 'paid', 'course_id' => $course->id, 'student_id' => $this->user('student')->id]);
        $pending = Enrollment::factory()->create(['payment_status' => 'pending', 'course_id' => $course->id, 'student_id' => $this->user('student')->id]);

        $this->actingAs($paid->student)->get(route('student.checkout.success', $paid))
            ->assertOk()->assertSee('Pembayaran Berhasil')->assertSee($course->title)->assertSee('Mulai Belajar');
        $this->actingAs($paid->student)->get(route('student.checkout.receipt', $paid))
            ->assertOk()->assertSee('Kuitansi Pembayaran')->assertSee('Rp 150.000')->assertSee('Lunas');

        $this->actingAs($pending->student)->get(route('student.checkout.success', $pending))->assertRedirect(route('student.checkout', $pending));
        $this->actingAs($pending->student)->get(route('student.checkout.receipt', $pending))->assertRedirect(route('student.checkout', $pending));

        $this->actingAs($this->user('student'))->get(route('student.checkout.success', $paid))->assertForbidden();
        $this->actingAs($this->user('student'))->getJson(route('student.checkout.status', $paid))->assertForbidden();
    }
}
