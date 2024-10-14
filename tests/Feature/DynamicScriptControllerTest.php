<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rule; // Assuming you have a Rule model for the rules
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DynamicScriptControllerTest extends TestCase
{
    use RefreshDatabase; // Use this trait to reset the database after each test

    /** @test */
    public function it_serves_dynamic_script_for_user_with_rules()
    {
        // Create a user with rules
        $user = User::factory()->create([
            'message' => 'Hello World!', // Example alert message
        ]);

        $rule1 = Rule::create([
            'user_id' => $user->id,
            'url_part' => 'abc',
            'condition' => 'contains',
        ]);

        $rule2 = Rule::create([
            'user_id' => $user->id,
            'url_part' => 'xyz',
            'condition' => 'starts_with',
        ]);

        // Make a request to the serveDynamicScript method
        $response = $this->get(route('serveDynamicScript', ['userId' => $user->id]));

        // Assert the response status is OK
        $response->assertStatus(200);

        // Assert the content type is JavaScript
        $response->assertHeader('Content-Type', 'application/javascript');

        // Check if the response contains the alert and rules logic
        $this->assertStringContainsString("alert('Hello World!');", $response->getContent());
        $this->assertStringContainsString("if (currentUrl.includes('abc')) { shouldShowAlert = true; }", $response->getContent());
        $this->assertStringContainsString("if (currentUrl.startsWith('xyz')) { shouldShowAlert = true; }", $response->getContent());
    }

    /** @test */
    public function it_throws_not_found_exception_for_invalid_user_id()
    {
        // Make a request with a non-existing user ID
        $response = $this->get(route('serveDynamicScript', ['userId' => 999]));

        // Assert the response status is 404
        $response->assertStatus(404);
    }
}
