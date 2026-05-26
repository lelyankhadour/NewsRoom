<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationSenderInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class DatabaseNotificationSender implements NotificationSenderInterface
{
    /**
     * Store a system notification in the database layer for administrators.
     */
    public function send(User $user, string $message): void
    {
        try {
            $user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\SystemAlert',
                'data' => [
                    'message' => $message,
                    'timestamp' => now()->toDateTimeString(),
                ],
                'read_at' => null,
            ]);

            Log::info('Database notification record created successfully for administrator.', [
                'id' => $user->id
            ]);

        } catch (Throwable $exception) {
            Log::error('Failed to write database notification logs inside service layer.', [
                'id' => $user->id,
                'error' => $exception->getMessage()
            ]);
            
            throw $exception;
        }
    }
}