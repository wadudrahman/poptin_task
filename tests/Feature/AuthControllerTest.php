<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_login_page(): void
    {
        $response = $this->get(route('showLogin'));

        $response->assertStatus(200);
        $response->assertViewIs('login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect(route('showLogin'));
        $response->assertSessionHas('failure', 'Provided credentials do not match.');
        $this->assertGuest();
    }

    public function test_show_registration_page(): void
    {
        $response = $this->get(route('showRegister'));

        $response->assertStatus(200);
        $response->assertViewIs('register');
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('showLogin'));
        $response->assertSessionHas('success', 'Account created successfully.');

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);
    }

    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_registration_fails_if_email_already_exists(): void
    {
        User::factory()->create([
            'email' => 'existinguser@example.com'
        ]);

        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'existinguser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }
}
