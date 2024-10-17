<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class DynamicScriptControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test serving a dynamic script with a valid UUID and cached script.
     */
    public function test_serve_dynamic_script_with_valid_uuid_and_cached_script()
    {
        // Generate a valid UUID
        $validUuid = Uuid::uuid4()->toString();

        // Simulate caching a script with the UUID as the key
        $cachedScript = 'console.log("This is a dynamic script!");';
        Cache::put($validUuid, $cachedScript);

        // Send a GET request to the dynamic script route
        $response = $this->get("/script/dynamic/{$validUuid}");

        // Assert the response is successful and the script is served
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/javascript');

        // Assert the exact raw script content is returned
        $response->assertSee('console.log("This is a dynamic script!");', false); // 'false' ensures no additional escaping
    }


    /**
     * Test serving a dynamic script with a valid UUID but no cached script.
     */
    public function test_serve_dynamic_script_with_valid_uuid_but_no_cached_script()
    {
        // Generate a valid UUID
        $validUuid = Uuid::uuid4()->toString();

        // Ensure the UUID is not cached
        Cache::forget($validUuid);

        // Send a GET request to the dynamic script route
        $response = $this->get("/script/dynamic/{$validUuid}");

        // Assert the response returns a 400 status with the error message
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid user uuid given.'
        ]);
    }

    /**
     * Test serving a dynamic script with an invalid UUID.
     */
    public function test_serve_dynamic_script_with_invalid_uuid()
    {
        // Generate an invalid UUID (e.g., incorrect format)
        $invalidUuid = 'invalid-uuid-string';

        // Send a GET request to the dynamic script route
        $response = $this->get("/script/dynamic/{$invalidUuid}");

        // Assert the response returns a 404 status since the invalid UUID won't match the route regex
        $response->assertStatus(404);
    }


    /**
     * Test serving a dynamic script with a valid UUID that does not match the regex.
     */
    public function test_serve_dynamic_script_with_invalid_uuid_regex()
    {
        // Generate a UUID-like string that does not match the regex pattern
        $invalidUuid = '1234-invalid-uuid';

        // Send a GET request to the dynamic script route
        $response = $this->get("/script/dynamic/{$invalidUuid}");

        // Assert that the request fails with a 404 status, as the regex should prevent access
        $response->assertStatus(404);
    }
}
