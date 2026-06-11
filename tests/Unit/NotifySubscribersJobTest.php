<?php
namespace Tests\Unit;

use App\Contracts\NotificationSenderInterface;
use App\Jobs\NotifySubscribersJob;
use App\Jobs\ProcessNotificationJob;
use App\Models\Article;
use App\Models\User;
use App\Services\Notifications\EmailNotificationSender;
use Mockery;
use Tests\TestCase;
/**
 * Test that ProcessNotificationJob correctly resolves the notification service 
 * and executes the send method.
 * I use Mockery::mock() instead of Laravel's built-in Fake methods 
 * (like Notification::fake()) because my  system utilizes a custom Strategy Pattern 
 * with a specialized NotificationSenderInterface. Since these services are not 
 * standard Laravel notification channels, the built-in Fakes cannot intercept them.
 * * Mocking the interface allows us to strictly define the contract and isolate the 
 * Job logic from the underlying delivery strategy (Database vs Email), 
 * ensuring our unit tests remain deterministic and fast.
 */


test('job executes the provided sender', function () {
    // Arrange
    $user = User::factory()->make(['id' => 1]);


    $mockSender = mock(NotificationSenderInterface::class);
// Assert
    $mockSender->shouldReceive('send')
        ->once()
        ->with($user, "Test");

   
    app()->instance(EmailNotificationSender::class, $mockSender);

    // Act
    $job = new ProcessNotificationJob($user, "Test", EmailNotificationSender::class);
    $job->handle();
    $this->assertTrue(true);

});