<?php

use App\Contracts\NotificationSenderInterface;
use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use App\Services\Notifications\DatabaseNotificationSender;
use App\Services\Notifications\EmailNotificationSender;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * I am using Mockery instead of Notification::fake() for this test.
 * Since the system uses custom services (injected via AppServiceProvider) to handle 
 * notifications, Notification::fake() cannot intercept these specific service calls. 
 * Mockery allows me to accurately test that the Listener correctly triggers my custom 
 * services, which keeps the original system design intact.
 */
uses(RefreshDatabase::class);

test('reader can add a comment to a published article', function () {
    // 1. Arrange:
    // $this->withoutExceptionHandling();
    $article = Article::factory()->create(['status' => ArticleStatus::PUBLISHED]);
    $reader = User::factory()->create(['role' => UserRole::READER]);
    
    $commentData = [
        'content' => 'content content'
    ];

    // 2. Act:  
    $this->actingAs($reader)
         ->postJson("/api/v1/articles/{$article->id}/comments", $commentData)
         ->assertStatus(201);

    // 3. Assert: 
    $this->assertDatabaseHas('comments', [
        'content' => $commentData['content'],
        'commentable_id' => $article->id,
        'commentable_type' => 'App\Models\Article',
        'user_id' => $reader->id,
    ]);
});

test('article author is notified when a new comment is added', function () {
    // 1. Arrange

    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $author = User::factory()->create(['role' => UserRole::WRITER]);
    $commenter = User::factory()->create(['role' => UserRole::READER]);
    $article = Article::factory()->create(['user_id' => $author->id]);

    // mock 
    $mockEmail = Mockery::mock(EmailNotificationSender::class);
    $mockDb = Mockery::mock(DatabaseNotificationSender::class);

    //relate mock with container 
    $this->app->instance(EmailNotificationSender::class, $mockEmail);
    $this->app->instance(DatabaseNotificationSender::class, $mockDb);

    $mockEmail->shouldReceive('send')
        ->atLeast()->once() 
        ->with(\Mockery::type(User::class), \Mockery::type('string'));

    $mockDb->shouldReceive('send')
        ->atLeast()->once() 
        // search why the listener act twic ??
        ->with(\Mockery::type(User::class), \Mockery::type('string'));
    // 2. Act

/**
 * I use the "sync" queue driver here because my notification system relies on
 * real Jobs, Listeners, and afterCommit observers. Using Queue::fake() would
 * block these components from running and produce incorrect test behavior.
 *
 * The sync driver lets the job execute immediately during the test while still
 * keeping the real queue untouched.
 */
    config(['queue.default' => 'sync']);
    
$response = $this->actingAs($commenter)
          ->postJson("/api/v1/articles/{$article->id}/comments", [
              'content' => 'content content'
          ]);

    // 3. Assert 

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('comments', ['content' => 'content content']);
});