<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\{JsonResponse, Response};
use Ramsey\Uuid\Uuid;

class DynamicScriptController extends Controller
{
    public function serveDynamicScript(string $userUuid): Response|JsonResponse
    {
        // Validate UUID
        if(!Uuid::isValid(trim($userUuid)) || !Cache::has($userUuid)) {
            return response()->json([
                'message' => 'Invalid user uuid given.'
            ], 400);
        }

        // Retrieve Script from Cache
        $script = Cache::get($userUuid);

        return response($script, 200)->header('Content-Type', 'application/javascript');
    }
}
