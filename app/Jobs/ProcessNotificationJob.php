<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
public int $tries = 3;
    public int $backoff = 15;
    public function __construct(
        protected User $user,
        protected string $message,
        protected string $senderClass 
     
    ) {}

    public function handle()
    {
       
        // $sender = new $this->senderClass();
        $sender = app($this->senderClass);
        $sender->send($this->user, $this->message);
    }
    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error("Notification Job failed after {$this->tries} attempts.", [
            'user_id' => $this->user->id,
            'sender'  => $this->senderClass,
            'error'   => $exception->getMessage()
        ]);
    }
}