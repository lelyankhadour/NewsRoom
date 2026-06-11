<?php

use App\Enums\UserRole;
use App\Notifications\NewCommentNotification;
use App\Models\User;
use App\Models\Comment;

describe('NewCommentNotification', function () {

    it('determines channels based on user role', function () {
        // Arrange
        $notification = new NewCommentNotification(new Comment());

        // Act & Assert: - Writer
        $writer = new User(['role' => UserRole::WRITER->value]);
        expect($notification->via($writer))->toBe(['mail']);

        // Act & Assert: - Admin
        $admin = new User(['role' => UserRole::ADMIN->value]);
        expect($notification->via($admin))->toBe(['database']);
    });

});
/**
 *  NOTE:
 * The actual notification system in this project does NOT rely on Laravel's
 * built‑in Notification feature. Instead, the application uses a fully custom
 * notification architecture based on a Strategy Pattern, implemented through
 * NotificationSenderInterface with Email and Database sender classes.
 *
 * This unit test exists ONLY because the task requirements explicitly asked
 * for testing the NewCommentNotification class. Therefore, this test verifies
 * the via() method in isolation, even though this Laravel Notification class
 * is NOT used by the real notification workflow in the application.
 */
