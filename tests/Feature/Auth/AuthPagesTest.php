<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_follows_the_new_design(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Halo, Selamat Datang')
            ->assertSee('Alamat Email')
            ->assertSee('Ingatkan Saya')
            ->assertSee('Lupa Password?')
            ->assertSee('Atau login dengan')
            ->assertSee('Lanjutkan dengan Google')
            ->assertSee(route('register'), false)
            ->assertSee('images/landing_page/pillar-1.png', false)
            ->assertSee('images/logo.svg', false);
    }

    public function test_register_page_follows_the_new_design(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Silahkan Membuat Akun')
            ->assertSee('Full Name')
            ->assertSee('Konfirmasi Password')
            ->assertSee('I agree to the Terms')
            ->assertSee('Continue with Google')
            ->assertSee('name="terms"', false)
            ->assertSee(route('login'), false);
    }

    public function test_register_form_keeps_old_input_but_never_passwords(): void
    {
        $response = $this->from('/register')->followingRedirects()->post('/register', [
            'name' => 'Dion', 'email' => 'dion@example.test',
            'password' => 'rahasia-banget-123', 'password_confirmation' => 'beda',
            'terms' => '1',
        ]);

        $response->assertSee('value="Dion"', false)->assertSee('value="dion@example.test"', false);
        $this->assertStringNotContainsString('rahasia-banget-123', $response->getContent());
    }

    public function test_login_page_shows_validation_error_in_indonesian_layout(): void
    {
        $this->from('/login')->followingRedirects()->post('/login', ['email' => 'x@example.test', 'password' => 'salah'])
            ->assertOk()
            ->assertSee('Halo, Selamat Datang');
    }
}
