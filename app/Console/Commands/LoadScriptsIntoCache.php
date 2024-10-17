<?php

namespace App\Console\Commands;

use App\Jobs\CacheScript;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LoadScriptsIntoCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:load-scripts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads existing user wise rules into cache';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            // Retrieve User(s)
            $userUuidPool = User::query()->whereHas('rules')
                ->pluck('uuid')
                ->toArray();

            // Dispatch Job
            foreach ($userUuidPool as $uuid) {
                CacheScript::dispatch($uuid);
            }
        } catch (\Exception $exception) {
            // Log Error
            Log::error('Error while storing rule: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }
    }
}
