<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_button_and_catalog_link(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('login'), false)
            ->assertSee('Masuk')
            ->assertSee('Lihat Kelas')
            ->assertDontSee('Daftar Sekarang');
    }

    public function test_logged_in_user_does_not_see_login_or_register(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertDontSee(route('login'), false)
            ->assertDontSee(route('register'), false)
            ->assertDontSee('>Masuk<', false)
            ->assertSee(route('dashboard'), false)
            ->assertSee('Dashboard');
    }
}
