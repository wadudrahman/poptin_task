<?php

namespace App\Jobs;

use App\Helpers\ScriptHelper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Ramsey\Uuid\Uuid;

class CacheScript implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $userUuid = '';

    /**
     * Create a new job instance.
     */
    public function __construct(string $userUuid)
    {
        $this->userUuid = $userUuid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Return If Proper User UUID Not Given; Or User UUID Don't Exists
        if (!Uuid::isValid($this->userUuid) || !User::query()->where('uuid', $this->userUuid)->exists()) {
            return;
        }

        // Deploy Script Helper Instance
        $scriptHelper = new ScriptHelper();

        // Execute Script Caching Process
        $scriptHelper->cacheScript($this->userUuid);
    }
}
