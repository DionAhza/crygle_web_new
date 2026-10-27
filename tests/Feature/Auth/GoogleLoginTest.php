<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        config(['services.google.client_id' => 'id', 'services.google.client_secret' => 'secret']);
    }

    private function fakeGoogle(array $overrides = [], bool $verified = true): void
    {
        $data = array_merge(['id' => 'g-123', 'name' => 'Budi Google', 'email' => 'budi@gmail.com', 'avatar' => 'https://lh3.googleusercontent.com/a.png'], $overrides);
        $user = (new SocialiteUser)->map($data);
        $user->user = ['email_verified' => $verified];

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_button_is_always_clickable_and_explains_missing_config(): void
    {
        $this->get('/login')->assertOk()->assertSee(route('auth.google'), false)->assertDontSee('Segera');

        config(['services.google.client_id' => null]);
        $this->get('/login')->assertOk()->assertSee(route('auth.google'), false);
        $this->get(route('auth.google'))->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Login dengan Google belum dikonfigurasi: GOOGLE_CLIENT_ID belum terbaca. Pastikan sudah diisi di file .env (bukan .env.example), lalu jalankan "php artisan config:clear".']);
    }

    public function test_redirect_goes_to_google(): void
    {
        $response = $this->get(route('auth.google'));

        $response->assertRedirect();
        $this->assertStringStartsWith('https://accounts.google.com/', $response->headers->get('Location'));
    }

    public function test_new_google_user_becomes_verified_student(): void
    {
        $this->fakeGoogle();

        $this->get(route('auth.google.callback'))->assertRedirect(route('dashboard'));

        $user = User::where('email', 'budi@gmail.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasRole('student'));
        $this->assertSame(['student'], $user->getRoleNames()->all());
        $this->assertSame('g-123', $user->google_id);
        $this->assertSame('Budi Google', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->avatar_url);

        // langsung bisa membuka area terproteksi 'verified'
        $this->get('/dashboard')->assertRedirect(route('student.courses'));
        $this->get(route('student.courses'))->assertOk();
    }

    public function test_existing_manual_account_is_linked_not_duplicated_and_keeps_role(): void
    {
        $mentor = User::factory()->unverified()->create(['email' => 'budi@gmail.com']);
        $mentor->assignRole('mentor');
        $this->fakeGoogle(['email' => 'Budi@Gmail.com']);

        $this->get(route('auth.google.callback'))->assertRedirect(route('dashboard'));

        $this->assertSame(1, User::count());
        $mentor->refresh();
        $this->assertAuthenticatedAs($mentor);
        $this->assertSame('g-123', $mentor->google_id);
        $this->assertNotNull($mentor->email_verified_at);
        $this->assertSame(['mentor'], $mentor->getRoleNames()->all());
    }

    public function test_returning_google_user_is_found_by_google_id_even_if_email_changed(): void
    {
        $user = User::factory()->create(['email' => 'lama@example.com']);
        $user->forceFill(['google_id' => 'g-123'])->save();
        $this->fakeGoogle(['email' => 'baru@gmail.com']);

        $this->get(route('auth.google.callback'))->assertRedirect(route('dashboard'));

        $this->assertSame(1, User::count());
        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_google_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'korban@gmail.com']);
        $this->fakeGoogle(['email' => 'korban@gmail.com'], verified: false);

        $this->get(route('auth.google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull(User::where('email', 'korban@gmail.com')->first()->google_id);
    }

    public function test_provider_failure_redirects_to_login_with_message(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andThrow(new RuntimeException('state mismatch'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_authenticated_users_cannot_use_google_routes(): void
    {
        $this->actingAs(User::factory()->create())->get(route('auth.google'))->assertRedirect();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_user_password_is_not_guessable_login(): void
    {
        $this->fakeGoogle();
        $this->get(route('auth.google.callback'));
        auth()->logout();

        $this->post('/login', ['email' => 'budi@gmail.com', 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
