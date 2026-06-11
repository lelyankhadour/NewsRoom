<?php

use App\Mail\ArticlePublishedMail;
use App\Models\Article;
use App\Models\User;

describe('ArticlePublishedMail', function () {

    it('has the correct subject and recipient', function () {
        // Arrange 
        $user = new User(['email' => 'writer@example.com']);
        $article = new Article(['title' => 'Test Article']);
    //   relate the article with the writer
        $article->setRelation('user', $user); 
        
        $mailable = new ArticlePublishedMail($article);

        // Act & Assert
        $mail = $mailable->build();

        expect($mail->subject)->toBe('Your article is published!')
            ->and($mailable->hasTo('writer@example.com'))->toBeTrue();
    });

});