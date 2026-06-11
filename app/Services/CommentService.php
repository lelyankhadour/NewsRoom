<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Article;
use App\Events\CommentCreated;
use Illuminate\Support\Facades\DB;

class CommentService
{
    public function createComment(array $data, Article $article, $user): Comment
    {
        return DB::transaction(function () use ($data, $article, $user) {
            $comment = $article->comments()->create([
                'content' => $data['content'],
                'user_id' => $user->id,
            ]);

              event(new CommentCreated($comment));

            return $comment;
        });
    }
}