<?php

namespace App\Http\Controllers;

use App\Helpers\ScriptHelper;
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

        // Retrieve Script from Cache; Otherwise from DB
        $script = null;
        if (Cache::has($userUuid)) {
            $script = Cache::get($userUuid);
        } else {
            $scriptHelper = new ScriptHelper();
            $script = $scriptHelper->cacheScript($userUuid, true);
        }

        return response($script, 200)->header('Content-Type', 'application/javascript');
    }
}
