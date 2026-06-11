<?php

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Article API Permissions', function () {

    test('unauthenticated user cannot view articles and gets 401', function () {
        // Act & Assert
        $this->getJson('/api/v1/articles')->assertStatus(401);
    });

    test('authenticated user can view articles and gets 200', function () {
        // Arrange
        $user = User::factory()->create();

        // Act & Assert
        $this->actingAs($user)
            ->getJson('/api/v1/articles')
            ->assertStatus(200);
    });

    test('only writer can create an article', function () {
        // Arrange
        $user = User::factory()->create(['role' => UserRole::READER]);

        // Act & Assert
        $this->actingAs($user)
            ->postJson('/api/v1/articles', ['title' => 'Test', 'content' => 'Content'])
            ->assertStatus(403);
    });

    test('writer can create an article and gets 201', function () {
        // Arrange
        $writer = User::factory()->create(['role' => UserRole::WRITER]);

        // Act & Assert
        $this->actingAs($writer, 'sanctum')
            ->postJson('/api/v1/articles', [
                'title' => 'This is a valid long title that meets the requirements',

                'content' => str_repeat('This is a very long content that is definitely more than one hundred characters long. ', 2),
                'status' => 'draft'
            ])
            ->assertStatus(201);
    });
    test('non-admin cannot delete an article', function () {
        // Arrange
        $writer = User::factory()->create(['role' => UserRole::WRITER]);
        $article = Article::factory()->create();

        // Act & Assert
        $this->actingAs($writer)
            ->deleteJson("/api/v1/articles/{$article->id}")
            ->assertStatus(403);
    });
});

describe('Article Lifecycle & Management', function () {

    test('article is stored in the database correctly', function () {
        // Arrange
        $writer = User::factory()->create(['role' => 'writer']);
        $articleData = [
            'title' => 'This is a valid long title',
            'content' => str_repeat('This is a valid content. ', 10),
            'status' => 'draft',
        ];

        // Act
        $this->actingAs($writer)
            ->postJson('/api/v1/articles', $articleData)
            ->assertStatus(201);

        // Assert
        $this->assertDatabaseHas('articles', [
            'title' => strtolower($articleData['title']),
            'user_id' => $writer->id,
        ]);
    });

    test('validation errors returned when creating article without required fields', function () {
        // Arrange
        $writer = User::factory()->create(['role' => UserRole::WRITER]);

        // Act & Assert
        $this->actingAs($writer)
            ->postJson('/api/v1/articles', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);
    });
});

describe('Soft Delete Functionality', function () {

    test('it can soft delete a model directly', function () {
        // Arrange
        $article = Article::factory()->create();

        // Act
        $article->delete();

        // Assert
        $this->assertNotNull($article->fresh()->deleted_at, "The model failed to set deleted_at even with direct delete()");
    });

    test('verify soft delete mechanism', function () {
        // Arrange
        $admin = User::factory()->create(['role' => \App\Enums\UserRole::ADMIN]);
        $article = Article::factory()->create();

        // Act
        $this->actingAs($admin)->deleteJson("/api/v1/articles/{$article->id}");

        // Assert
        $this->assertSoftDeleted('articles', ['id' => $article->id]);

        $articleInDb = \Illuminate\Support\Facades\DB::table('articles')
            ->where('id', $article->id)
            ->first();

        $this->assertNotNull($articleInDb->deleted_at, "Database value is null despite SoftDelete success");
    });
});
test('reader cannot create an article and gets 403', function () {
    $reader = User::factory()->create(['role' => UserRole::READER]);

    $this->actingAs($reader, 'sanctum')
        ->postJson('/api/v1/articles', [ 
            'title' => 'Test Article',
            'body' => 'Test Body'
        ])
        ->assertStatus(403);
});
test('soft deleted articles are not visible in list', function () {
    $article = Article::factory()->create();
    $article->delete(); // Soft delete

    $this->actingAs(User::factory()->create())
        ->getJson('/api/articles')
        ->assertJsonMissing(['id' => $article->id]); 
});