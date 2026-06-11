<?php

use App\Models\User;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('Article Creation with Attachments', function () {

    it('creates an article with attachments and stores files physically in secure path', function () {
        // Arrange
        Storage::fake('public');
        $user = User::factory()->create();
        
        $file1 = UploadedFile::fake()->image('test1.jpg');
        $file2 = UploadedFile::fake()->image('test2.jpg');

        $data = [
            'title' => 'title title',
            'content' => 'content content',
            'attachments' => [$file1, $file2]
        ];

        // Act
        $article = app(ArticleService::class)->createArticle($data, $user->id);

        // Assert
    
        expect($article->attachments)->toHaveCount(2);


        foreach ($article->attachments as $attachment) {
            Storage::disk('public')->assertExists($attachment->file_path);
            
                     expect($attachment->file_path)->toStartWith('attachments/');
        }
    });

});