<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationSenderInterface;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message; // This import explicitly resolves the closure parameter type hint
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailNotificationSender implements NotificationSenderInterface
{
    /**
     * Send a raw email notification securely to the user context.
     * * @param User $user
     * @param string $message
     * @return void
     */
    public function send(User $user, string $message): void
    {
        try {
    
            Mail::raw($message, function (Message $mail) use ($user) {
                $mail->to($user->email)
                     ->subject('TechNova Platform Update');
            });

            Log::info('Email notification sent successfully via Mail raw facade.', [
                'user_id' => $user->id
            ]);

        } catch (Throwable $exception) {
            Log::error('Failed to execute email notification delivery within service layer.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage()
            ]);
            
            throw $exception;
        }
    }
}