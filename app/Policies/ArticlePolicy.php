<?php

namespace App\Policies;

use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    
    public function update(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->role === UserRole::ADMIN;
    }

       public function delete(User $user, Article $article): bool
    {
        \Log::info('Policy Check:', [
        'user_id' => $user->id,
        'user_role' => $user->role,
        'article_user_id' => $article->user_id,
        'is_admin' => ($user->role === UserRole::ADMIN)
    ]);
        return $user->id === $article->user_id || $user->role === UserRole::ADMIN;
    }
    public function view(User $user, Article $article): bool
{
    return $article->status === ArticleStatus::PUBLISHED || $user->id === $article->user_id || $user->role === UserRole::ADMIN;
}
}