<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\ProcessNotificationJob;
use App\Models\User;
use App\Services\Notifications\EmailNotificationSender;
use App\Services\Notifications\DatabaseNotificationSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Exception;

class SendWelcomeNotificationListener implements ShouldQueue
{   public string  $queue = 'low';
    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        try {
            // 1. Dispatch welcome email job for the newly registered user
            ProcessNotificationJob::dispatch(
                $event->user,
                'Welcome to TechNova Newsroom',
                EmailNotificationSender::class
            );

            // 2. Dispatch admin notification job for system monitoring
            $admin = User::where('role', 'admin')->first();
            
            if ($admin) {
                ProcessNotificationJob::dispatch(
                    $admin,
                    "New user registered: " . $event->user->email,
                    DatabaseNotificationSender::class
                );
            }
        } catch (Exception $e) {
              Log::error('Failed to send registration notifications: ' . $e->getMessage(), [
                'user_id' => $event->user->id,
                'exception' => $e
            ]);
        }
    }
}