<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRulesRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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

    public function storeRules(StoreRulesRequest $request): RedirectResponse
    {
        // Validation Rules
        $validatedData = $request->validated();

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
