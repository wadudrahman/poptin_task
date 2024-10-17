<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ScriptHelper
{
    private function generatesScript(string $userUuid): string
    {
        // Fetch user data with rules
        $userData = User::query()->with('rules')
            ->where('uuid', $userUuid)
            ->firstOrFail();

        // Slice Rules and Message
        $rules = $userData->rules;
        $alertText = $userData->message;

        // Separate rules into show
        $showRules = $rules->where('action', 'show');

        // Built JS Script
        $generateScript = "
        window.onload = function() {
            var currentPath = window.location.pathname;
            var pathSegments = currentPath.split('/').filter(Boolean);
            var firstWord = pathSegments[0] || '';
            var lastWord = pathSegments[pathSegments.length - 1] || '';
            var exactPath = currentPath.startsWith('/') ? currentPath.substring(1) : currentPath;
            var shouldShowAlert = false;

            // Remove trailing slash if present
            if (exactPath.endsWith('/')) {
                exactPath = exactPath.slice(0, -1);
            }
        ";

        // Generate JS for show rules
        foreach ($showRules as $rule) {
            $generateScript .= $this->generateShowRuleJs($rule, 'firstWord', 'lastWord', 'exactPath');
        }

        $generateScript .= "
            if (shouldShowAlert) {
                alert(" . json_encode($alertText) . ");
            }
        };";

        return $generateScript;
    }

    private function generateShowRuleJs($rule, $firstWordVar, $lastWordVar, $exactPathVar): string
    {
        $url = json_encode($rule->url);
        return match ($rule->condition) {
            'contains' => "if (currentPath.includes($url)) { shouldShowAlert = true; }\n",
            'starts_with' => "if ($firstWordVar === $url || firstWord.includes($url)) { shouldShowAlert = true; }\n",
            'ends_with' => "if ($lastWordVar === $url || lastWord.endsWith($url)) { shouldShowAlert = true; }\n",
            'exact' => "if ($exactPathVar === $url) { shouldShowAlert = true; }\n",
            default => "",
        };
    }

    public function cacheScript(string $userUuid, bool $returnScript = false): ?string
    {
        // Generate Script
        $generatedScript = $this->generatesScript($userUuid);

        // Forget Old cached script if exists
        Cache::forget($userUuid);

        // Cache the new script
        Cache::put($userUuid, $generatedScript);

        if ($returnScript) {
            return $generatedScript;
        }

        return null;
    }
}
