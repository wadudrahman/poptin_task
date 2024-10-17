<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the login page loads successfully.
     */
    public function test_show_login_page()
    {
        $response = $this->get(route('showLogin'));

        $response->assertStatus(200);
        $response->assertViewIs('login');
    }

    /**
     * Test user login with valid credentials.
     */
    public function test_login_successfully()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('password'), // Hashed password
        ]);

        // Submit login form with valid credentials
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Assert the user is authenticated and redirected to the dashboard
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_login_with_invalid_credentials()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        // Attempt login with incorrect password
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // Assert user is redirected back to login page with failure message
        $response->assertRedirect(route('showLogin'));
        $response->assertSessionHas('failure', 'Provided credentials do not match.');
        $this->assertGuest();
    }

    /**
     * Test the registration page loads successfully.
     */
    public function test_show_registration_page()
    {
        $response = $this->get(route('showRegister'));

        $response->assertStatus(200);
        $response->assertViewIs('register');
    }

    /**
     * Test user registration with valid data.
     */
    public function test_register_user_successfully()
    {
        // Submit registration form with valid data
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        // Assert the user is created and redirected to login
        $response->assertRedirect(route('showLogin'));
        $response->assertSessionHas('success', 'Account created successfully.');

        // Ensure the user is in the database
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    /**
     * Test registration with an already existing email.
     */
    public function test_register_with_existing_email_fails()
    {
        // Create
        User::factory()->create([
            'email' => 'testuser@example.com',
        ]);

        // Attempt to register with the same email
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }


    /**
     * Test logging out a user successfully.
     */
    public function test_logout_user_successfully()
    {
        // Create a user and log in
        $user = User::factory()->create();
        $this->actingAs($user);

        // Send POST request to logout
        $response = $this->post(route('logout'));

        // Assert the user is logged out and redirected to login page
        $response->assertRedirect(route('showLogin'));
        $response->assertSessionHas('success', 'You have been logged out successfully.');
        $this->assertGuest();
    }
}
