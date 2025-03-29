<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '123456789',
            'address' => '123 Main Street',
            'role' => 'user',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect(route('welcome'));
    }

    public function test_registration_fails_without_required_fields(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            // Missing phone, address, and role
        ]);

        $response->assertSessionHasErrors(['phone', 'address', 'role']);
        $this->assertGuest();
    }

}
