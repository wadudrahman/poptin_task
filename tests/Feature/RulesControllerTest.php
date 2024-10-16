<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

class RulesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the dashboard loads and retrieves rules.
     */
    public function test_dashboard_loads_with_rules()
    {
        // Create a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some rules for the user
        $rules = Rule::factory()->count(3)->create(['user_id' => $user->id]);

        // Assert that the dashboard loads and contains the user's rules
        $response = $this->get('/rules/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('rules', $user->rules);
    }


    /**
     * Test storing new rules and updating existing ones.
     */
    public function test_store_rules_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Prepare valid data for both new and updated rules
        $existingRule = Rule::factory()->create(['user_id' => $user->id, 'uuid' => Uuid::uuid4()->toString()]);

        $data = [
            'uuid' => [$existingRule->uuid, null], // One existing and one new
            'action' => ['hide', 'show'],
            'rule' => ['exact', 'contains'],
            'url' => ['exact-url', 'new-url'],
            'message' => 'Test Message',
        ];

        // Send a POST request to store rules
        $response = $this->post('/rules/store', $data);

        // Assert redirection and success message
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Rules saved successfully!');

        // Assert the existing rule was updated
        $this->assertDatabaseHas('rules', [
            'uuid' => $existingRule->uuid,
            'action' => 'hide',
            'condition' => 'exact',
            'url' => 'exact-url'
        ]);

        // Assert the new rule was created
        $this->assertDatabaseHas('rules', [
            'action' => 'show',
            'condition' => 'contains',
            'url' => 'new-url'
        ]);

        // Assert the user's message is updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'message' => 'Test Message'
        ]);
    }

    /**
     * Test validation errors when storing rules.
     */
    public function test_store_rules_validation_errors()
    {
        // Create a user and log in
        $user = User::factory()->create();
        $this->actingAs($user);

        // Prepare invalid data (missing the 'message' field)
        $data = [
            'action' => ['show'],
            'rule' => ['contains'],
            'url' => ['test-url'],
        ];

        // Send a POST request to /rules/store
        $response = $this->post('/rules/store', $data);

        // Assert validation errors for 'message' field
        $response->assertSessionHasErrors('message');
    }


    /**
     * Test deleting a rule successfully.
     */
    public function test_delete_rule_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a rule for the user
        $rule = Rule::factory()->create(['user_id' => $user->id]);

        // Send a DELETE request to destroy the rule
        $response = $this->deleteJson('/rules/destroy/' . $rule->uuid);

        // Assert the rule was deleted
        $response->assertStatus(200);
        $response->assertJson(['message' => 'The rule deleted successfully.']);

        // Ensure the rule is deleted from the database
        $this->assertDatabaseMissing('rules', ['uuid' => $rule->uuid]);
    }

    /**
     * Test deleting a non-existent rule.
     */
    public function test_delete_rule_not_found()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a non-existent UUID
        $fakeUuid = \Ramsey\Uuid\Uuid::uuid4()->toString();

        // Send a DELETE request with a non-existent UUID
        $response = $this->deleteJson('/rules/destroy/' . $fakeUuid);

        // Assert the response is 404 and the rule is not found
        $response->assertStatus(404);
        $response->assertJson(['message' => 'The rule not found or you do not own the rule.']);
    }


    /**
     * Test deleting a rule that the user does not own.
     */
    public function test_delete_rule_not_owned_by_user()
    {
        // Create two users
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create a rule for the other user
        $rule = Rule::factory()->create(['user_id' => $otherUser->id]);

        // Act as the first user
        $this->actingAs($user);

        // Try deleting the other user's rule
        $response = $this->deleteJson('/rules/destroy/' . $rule->uuid);

        // Assert the response is 404 and the rule is not found or owned by the user
        $response->assertStatus(404);
        $response->assertJson(['message' => 'The rule not found or you do not own the rule.']);
    }
}
