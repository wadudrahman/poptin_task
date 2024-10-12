<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class DynamicScriptController extends Controller
{
    public function serveDynamicScript(string $userId): Response
    {
        $userData = User::query()->with('rules')->findOrFail($userId);
        $rules = $userData->rules;
        $alertText = $userData->message;

        // Logic to generate JS based on the user's rules
        $js = "
    window.onload = function() {
        var currentUrl = window.location.href;
        var shouldShowAlert = false;
    ";

        foreach ($rules as $rule) {
            $js .= $this->generateRuleJs($rule);
        }

        $js .= "
        if (shouldShowAlert) {
            alert('{$alertText}');
        }
    };
    ";

        return response($js, 200)->header('Content-Type', 'application/javascript');
    }

    private function generateRuleJs($rule)
    {
        switch ($rule->condition) {
            case 'contains':
                return "if (currentUrl.includes('{$rule->url_part}')) { shouldShowAlert = true; }";
            case 'starts_with':
                return "if (currentUrl.startsWith('{$rule->url_part}')) { shouldShowAlert = true; }";
            case 'ends_with':
                return "if (currentUrl.endsWith('{$rule->url_part}')) { shouldShowAlert = true; }";
            case 'exact':
                return "if (currentUrl === '{$rule->url_part}') { shouldShowAlert = true; }";
        }
    }
}
