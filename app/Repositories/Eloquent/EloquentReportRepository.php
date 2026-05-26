<?php

namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Contracts\ReportRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class EloquentReportRepository implements ReportRepositoryInterface
{
    protected Article $articleModel;
    protected User $userModel;

    public function __construct(Article $articleModel, User $userModel)
    {
        $this->articleModel = $articleModel;
        $this->userModel = $userModel;
    }

    public function findById(int $id): ?Model { return $this->articleModel->find($id); }
    public function create(array $data): Model { return $this->articleModel->create($data); }
    public function update(Model $model, array $data): bool { return $model->update($data); }
    public function delete(Model $model): bool { return (bool) $model->delete(); }

    /**
     * Fetch calculated publishing statistics dynamically.
     */
    public function getPublishedArticlesCount(int $days): int
    {
        return $this->articleModel
            ->where('status', ArticleStatus::PUBLISHED->value)
            ->where('published_at', '>=', now()->subDays($days))
            ->count();
    }

    /**
     * Compile ranking metrics for system content creators.
     */
    public function getTopWriters(int $limit = 5): array
    {
        return $this->userModel
            ->where('role', UserRole::WRITER->value)
            ->withCount(['articles' => function ($query) {
                $query->where('status', ArticleStatus::PUBLISHED->value);
            }])
            ->orderBy('articles_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'writer_id'     => $user->id,
                    'writer_name'   => $user->name,
                    'articles_count'=> $user->articles_count,
                ];
            })
            ->toArray();
    }


public function getTotalCommentsCount(): int
{
    return Comment::count();
}
/**
     * Fetch all necessary statistics for the dashboard.
     */
    public function getDashboardStats(): array
    {
        return [
            'total_published_articles' => $this->articleModel->where('status', ArticleStatus::PUBLISHED->value)->count(),
            'total_comments'           => Comment::count(),
           'top_writers'              => $this->getTopWriters(5),
            'total_writers'            => $this->userModel->where('role', UserRole::WRITER->value)->count(),
        ];
    }
    public function getPublishedArticlesSince(int $days): array
{
    $count = $this->articleModel
        ->where('status', ArticleStatus::PUBLISHED->value)
        ->where('published_at', '>=', now()->subDays($days))
        ->count();

    return [
        'count' => $count,
        'period' => $days . ' days'
    ];
}
    
}