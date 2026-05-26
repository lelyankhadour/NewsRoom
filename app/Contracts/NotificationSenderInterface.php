<?php

namespace App\Contracts;

use App\Models\User;

interface NotificationSenderInterface
{
    /**
     * Send a notification to a specific user with the given data context.
     *
     * @param User $user
     * @param array $message
     * @return void
     */
    public function send(User $user, string $message): void;
}