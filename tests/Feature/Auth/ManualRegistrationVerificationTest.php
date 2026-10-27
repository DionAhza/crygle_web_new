<?php

namespace Tests\Feature\Auth;

use App\Models\Course;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ManualRegistrationVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function register(): User
    {
        $this->post('/register', [
            'name' => 'Siswa Manual', 'email' => 'manual@example.com', 'password' => 'password-123',
            'password_confirmation' => 'password-123', 'terms' => '1',
        ]);

        return User::where('email', 'manual@example.com')->firstOrFail();
    }

    public function test_manual_registration_sends_verification_email_and_starts_unverified(): void
    {
        Notification::fake();

        $user = $this->register();

        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_is_blocked_from_protected_pages_until_verified(): void
    {
        $user = $this->register();
        $course = Course::factory()->create(['price' => 0]);

        $this->get('/dashboard')->assertRedirect(route('verification.notice'));
        $this->get(route('student.courses'))->assertRedirect(route('verification.notice'));
        $this->post(route('student.enroll', $course))->assertRedirect(route('verification.notice'));
        $this->assertDatabaseCount('enrollments', 0);

        // katalog publik tetap bisa dibuka
        $this->get(route('catalog.index'))->assertOk();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['id' => $user->id, 'hash' => sha1($user->email)]);
        $this->get($url)->assertRedirect();

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->get('/dashboard')->assertRedirect(route('student.courses'));
        $this->post(route('student.enroll', $course))->assertRedirect(route('student.courses'));
        $this->assertDatabaseCount('enrollments', 1);
    }

    public function test_verification_link_with_wrong_hash_does_not_verify(): void
    {
        $user = $this->register();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['id' => $user->id, 'hash' => sha1('lain@example.com')]);

        $this->get($url)->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_email_can_be_resent(): void
    {
        $user = $this->register();
        Notification::fake();

        $this->post(route('verification.send'))->assertRedirect()->assertSessionHas('status', 'verification-link-sent');
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
