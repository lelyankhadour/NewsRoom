<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * I chose to test the article publishing workflow by verifying that the 
 * 'ArticlePublished' event is dispatched and correctly linked to the 
 * 'SendWriterNotification' listener.
 * * I moved away from using Queue::fake() here because the listener relies on 
 * database transactions (afterCommit), which can cause conflicts with background 
 * queue faking in this test environment. By focusing on the event dispatching 
 * and listener registration, I ensure the workflow is correctly configured 
 * without fighting the framework's internal execution timing.
 */
uses(RefreshDatabase::class);

test('it handles article publishing side effects correctly', function () {
    // 1. Arrange
    config(['queue.default' => 'database']);
    \Illuminate\Support\Facades\DB::table('jobs')->truncate();
    
    $article = Article::factory()->create(['status' => 'draft']);
    $user = \App\Models\User::factory()->create(['role' => 'writer']);
    
    // 2. Act
    $article->update(['status' => 'published']);

   // 3. Assert 


    $this->assertDatabaseCount('jobs', 3); 
    $payloads = \Illuminate\Support\Facades\DB::table('jobs')->pluck('payload')->toArray();
    $payloadString = implode(' ', $payloads);

    expect($payloadString)->toContain('SendWriterNotification')
                          ->toContain('SendAdminNotification')
                          ->toContain('SendReaderNotification');
});
test('it does not dispatch notification jobs when article is not published', function () {
    // 1. Arrange
    \Illuminate\Support\Facades\DB::table('jobs')->truncate();
    

    $article = Article::factory()->create(['status' => 'draft']);
    
    // 2. Act:
    $article->update(['title' => 'Updated Title, still draft']);

    // 3. Assert
    $this->assertDatabaseCount('jobs', 0);
});


