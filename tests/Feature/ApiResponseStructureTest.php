<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class);
it('logs api requests in the database', function () {
// 1. Arrange
    $user = User::factory()->create();
    
    // 2. Act
    $this->actingAs($user)->getJson('/api/v1/articles');

  // 3. Assert 
    $this->assertDatabaseHas('api_request_logs', [
        'method' => 'GET',
        'status_code' => 200,
    ]);
});