<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRulesRequest;
use App\Models\Rule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\{DB, Log};
use Ramsey\Uuid\Uuid;

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

        // Default operation status and message
        $operationStatus = 'success';
        $operationMessage = 'Rules saved successfully!';

        try {
            // Retrieve User Data
            $userData = auth()->user();

            // Start a DB transaction
            DB::beginTransaction();

            $newRulePool = [];

            // Prepare the data and either update or create the rules
            foreach ($validatedData['action'] as $index => $action) {
                // Update Existing Rule
                if (isset($validatedData['uuid'][$index])) {
                    $userData->rules()->where('uuid', trim($validatedData['uuid'][$index]))
                        ->update([
                            'action' => $action,
                            'condition' => $validatedData['rule'][$index],
                            'url' => trim($validatedData['url'][$index], "/")
                        ]);

                    continue;
                }

                // Create New Rule
                $newRulePool[] = [
                    'uuid' => Uuid::uuid4()->toString(),
                    'user_id' => $userData->id,
                    'action' => $action,
                    'condition' => $validatedData['rule'][$index],
                    'url' => trim($validatedData['url'][$index], "/")
                ];
            }

            // Insert New Rule Pool
            if (!empty($newRulePool)) {
                Rule::query()->insert($newRulePool);
            }

            // Update Message
            if ($userData->message != trim($validatedData['message'])) {
                $userData->update(['message' => trim($validatedData['message'])]);
            }

            // Commit the DB transaction
            DB::commit();
        } catch (\Exception $exception) {
            // Rollback the DB transaction if something goes wrong
            DB::rollBack();

            // Log the exception with detailed error information
            Log::error('Error while storing rules: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            // Update operation status and message
            $operationStatus = 'failure';
            $operationMessage = 'Rules saving failed!';
        }

        return redirect()->back()->with($operationStatus, $operationMessage);
    }

    public function destroyRule(string $uuid): JsonResponse
    {
        try {
            // Retrieve User Data
            $userData = auth()->user();

            // Delete Rule
            $deleted = Rule::query()
                ->where('user_id', $userData->id)
                ->where('uuid', $uuid)
                ->delete();

            // Uplift Flags
            $status = $deleted ? 200 : 404;
            $message = $deleted ? 'The rule deleted successfully.' : 'The rule not found or you do not own the rule.';

        } catch (\Exception $exception) {
            // Log Error
            Log::error('Error while storing rule: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);

            // Uplift Flags
            $status = 500;
            $message = 'An error occurred while deleting the rule.';
        }

        return response()->json(['message' => $message], $status);
    }
}
