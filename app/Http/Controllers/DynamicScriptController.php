<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class DynamicScriptController extends Controller
{
    public function serveDynamicScript(string $userId): Response
    {
        // Fetch user data with rules
        $userData = User::query()->with('rules')->findOrFail($userId);
        $rules = $userData->rules;
        $alertText = $userData->message;

        // Separate rules into show and hide
        $showRules = $rules->where('action', 'show');
        $hideRules = $rules->where('action', 'hide');

        // JS Logic
        $js = "
        window.onload = function() {
            var currentPath = window.location.pathname;
            var pathSegments = currentPath.split('/').filter(Boolean);
            var firstWord = pathSegments[0] || '';
            var lastWord = pathSegments[pathSegments.length - 1] || '';
            var exactPath = currentPath.startsWith('/') ? currentPath.substring(1) : currentPath;

            // Remove trailing slash if present
            if (exactPath.endsWith('/')) {
                exactPath = exactPath.slice(0, -1);
            }

            var shouldShowAlert = false;
            var shouldHideAlert = false;
        ";

        // Generate JS for hide rules
        foreach ($hideRules as $rule) {
            $js .= $this->generateHideRuleJs($rule, 'firstWord', 'lastWord', 'exactPath');
        }

        // Generate JS for show rules
        foreach ($showRules as $rule) {
            $js .= $this->generateShowRuleJs($rule, 'firstWord', 'lastWord', 'exactPath');
        }

        $js .= "
            if (!shouldHideAlert && shouldShowAlert) {
                alert(" . json_encode($alertText) . ");
            } else {
                console.log('Alert not shown due to hide rule or no show rule matched.');
            }
        };
        ";

        return response($js, 200)->header('Content-Type', 'application/javascript');
    }

    private function generateShowRuleJs($rule, $firstWordVar, $lastWordVar, $exactPathVar)
    {
        $url = json_encode($rule->url);
        switch ($rule->condition) {
            case 'contains':
                return "if (currentPath.includes($url)) { shouldShowAlert = true; console.log('Show Alert Rule Matched: Contains $url'); }\n";
            case 'starts_with':
                return "if ($firstWordVar === $url) { shouldShowAlert = true; console.log('Show Alert Rule Matched: Starts With $url'); }\n";
            case 'ends_with':
                return "if ($lastWordVar === $url) { shouldShowAlert = true; console.log('Show Alert Rule Matched: Ends With $url'); }\n";
            case 'exact':
                return "if ($exactPathVar === $url) { shouldShowAlert = true; console.log('Show Alert Rule Matched: Exact $url'); }\n";
            default:
                return "";
        }
    }

    private function generateHideRuleJs($rule, $firstWordVar, $lastWordVar, $exactPathVar)
    {
        $url = json_encode($rule->url);
        switch ($rule->condition) {
            case 'contains':
                return "if (currentPath.includes($url)) { shouldHideAlert = true; console.log('Hide Alert Rule Matched: Contains $url'); }\n";
            case 'starts_with':
                return "if ($firstWordVar === $url) { shouldHideAlert = true; console.log('Hide Alert Rule Matched: Starts With $url'); }\n";
            case 'ends_with':
                return "if ($lastWordVar === $url) { shouldHideAlert = true; console.log('Hide Alert Rule Matched: Ends With $url'); }\n";
            case 'exact':
                return "if ($exactPathVar === $url) { shouldHideAlert = true; console.log('Hide Alert Rule Matched: Exact $url'); }\n";
            default:
                return "";
        }
    }
}
