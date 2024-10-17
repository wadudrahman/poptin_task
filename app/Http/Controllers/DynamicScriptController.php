<?php

namespace App\Http\Controllers;

use App\Helpers\ScriptHelper;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\{JsonResponse, Response};
use Ramsey\Uuid\Uuid;

class DynamicScriptController extends Controller
{
    public function serveDynamicScript(User $user): Response|JsonResponse
    {
        // Retrieve User UUID
        $userUuid = $user->uuid;

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
