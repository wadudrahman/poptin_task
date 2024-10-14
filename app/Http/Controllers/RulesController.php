<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\{DB, Log};

class RulesController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }
    public function showDashboard(): View
    {
        // Retrieve Rules
        $rules = auth()->user()->rules()->get();

        return view('dashboard', compact('rules'));
    }

    public function storeRules(Request $request): RedirectResponse
    {
        // Validation Rules
        $validatedData = $request->validate([
            'message' => 'required|string|max:255',
            'action.*' => 'required|in:show,hide',
            'rule.*' => 'required|in:contains,starts_with,ends_with,exact',
            'url.*' => 'required|string|max:255',
        ], [
            'message.required' => 'The message text is required.',
            'message.string' => 'The message text must be a valid string.',
            'message.max' => 'The message text must not exceed 255 characters.',
            'action.*.required' => 'The action field is required.',
            'action.*.in' => 'The action must be either "show" or "hide".',
            'rule.*.required' => 'The condition field is required.',
            'rule.*.in' => 'The condition must be one of the following: contains, starts with, ends with, exact.',
            'url.*.required' => 'The URL part field is required.',
            'url.*.string' => 'The URL part must be a string.',
            'url.*.max' => 'The URL part must not exceed 255 characters.'
        ]);

        // Define Alert Params
        $operationStatus = 'success';
        $operationMessage = 'Rules saved successfully!';

        try {
            // Retrieve User Data
            $userData = auth()->user();

            // Delete Existing Rules
            $userData->rules()->delete();

            // DB Transaction
            DB::beginTransaction();

            $data = [];

            // Save Rules @ DB
            foreach ($validatedData['action'] as $index => $action) {
                $userData->rules()->create([
                    'action' => $action,
                    'condition' => $validatedData['rule'][$index],
                    'url' => trim($validatedData['url'][$index], "/")
                ]);
            }

            // Save Message @ DB
            $userData->update(['message' => trim($validatedData['message'])]);

            // Persist DB Changes
            DB::commit();
        } catch (\Exception $exception) {
            // Rollback DB Changes
            DB::rollBack();

            // Log Error
            Log::error('Error while storing rule: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);

            // Update Operation Status and Message
            $operationStatus = 'failure';
            $operationMessage = 'Rules saving failed!';
        }

        return redirect()->back()->with($operationStatus, $operationMessage);
    }
}
