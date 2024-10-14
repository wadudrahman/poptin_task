<?php

namespace Tests\Feature;

use App\Models\{User, Rule};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RulesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_dashboard_displays_user_rules(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create some rules for the user
        $rules = Rule::factory()->count(3)->create(['user_id' => $user->id]);

        // Acting as the authenticated user
        $response = $this->actingAs($user)->get(route('dashboard'));

        // Assert status and view
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas('rules', $user->rules);
    }

    public function test_store_rules_with_valid_data(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Simulated input data
        $postData = [
            'message' => 'This is a test message',
            'action' => ['show', 'hide'],
            'rule' => ['contains', 'starts_with'],
            'url' => ['example.com', 'test.com'],
        ];

        // Acting as the authenticated user
        $response = $this->actingAs($user)->post(route('storeRules'), $postData);

        // Assert successful redirect and session message
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Rules saved successfully!');

        // Assert rules were saved to the database
        $this->assertDatabaseHas('rules', [
            'user_id' => $user->id,
            'action' => 'show',
            'condition' => 'contains',
            'url' => 'example.com'
        ]);

        $this->assertDatabaseHas('rules', [
            'user_id' => $user->id,
            'action' => 'hide',
            'condition' => 'starts_with',
            'url' => 'test.com'
        ]);

        // Assert the message was updated for the user
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'message' => 'This is a test message'
        ]);
    }

    public function test_store_rules_validation_errors(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Invalid data (missing required fields)
        $postData = [
            'message' => '', // message is required
            'action' => ['show'],
            'rule' => ['invalid_rule'], // invalid rule
            'url' => [''], // URL is required
        ];

        // Acting as the authenticated user
        $response = $this->actingAs($user)->post(route('storeRules'), $postData);

        // Assert validation errors
        $response->assertSessionHasErrors(['message', 'rule.0', 'url.0']);
    }

    public function test_store_rules_deletes_existing_rules_before_saving_new_ones(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create some existing rules
        Rule::factory()->count(2)->create(['user_id' => $user->id]);

        // Simulated new input data
        $newData = [
            'message' => 'Updated message',
            'action' => ['show'],
            'rule' => ['exact'],
            'url' => ['new-url.com'],
        ];

        // Acting as the authenticated user
        $response = $this->actingAs($user)->post(route('storeRules'), $newData);

        // Assert successful redirect
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Rules saved successfully!');

        // Assert the new rules were saved
        $this->assertDatabaseHas('rules', [
            'user_id' => $user->id,
            'action' => 'show',
            'condition' => 'exact',
            'url' => 'new-url.com'
        ]);

        // Assert the old rules were deleted (only new rule should exist)
        $this->assertDatabaseCount('rules', 1);
    }
}
