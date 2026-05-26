<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckArticleVisibilityMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $articleParam = $request->route('article') ?? $request->route('id');
        $article = $articleParam instanceof Article 
            ? $articleParam 
            : Article::withoutGlobalScopes()->find($articleParam);

        if (!$article) {
            return $this->errorResponse('Article not found.', Response::HTTP_NOT_FOUND);
        }

        $statusValue = $article->status instanceof ArticleStatus ? $article->status->value : $article->status;
        
        if ($statusValue === ArticleStatus::PUBLISHED->value) {
            return $next($request);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->errorResponse('Unauthorized to view this draft.', Response::HTTP_FORBIDDEN);
        }

        $userRole = $user->role instanceof UserRole ? $user->role->value : $user->role;

        if ($userRole === UserRole::ADMIN->value || (int)$user->id === (int)$article->user_id) {
            $request->attributes->set('shared_article', $article);
            return $next($request);
        }

        return $this->errorResponse('Unauthorized to view this draft.', Response::HTTP_FORBIDDEN);
    }
}