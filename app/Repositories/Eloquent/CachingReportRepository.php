<?php

namespace App\Repositories\Eloquent;

use App\Contracts\ReportRepositoryInterface;
use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CachingReportRepository implements ReportRepositoryInterface
{
    protected ReportRepositoryInterface $next;

    public function __construct(ReportRepositoryInterface $next) 
    { 
        $this->next = $next; 
    }

    public function getDashboardStats(): array
    {
        return Cache::tags(['dashboard'])->remember('stats', 600, function () {
            return $this->next->getDashboardStats();
        });
    }

    public function getPublishedArticlesCount(int $days): int { return $this->next->getPublishedArticlesCount($days); }
    public function getTopWriters(int $limit = 5): array { return $this->next->getTopWriters($limit); }
    public function getTotalCommentsCount(): int { return $this->next->getTotalCommentsCount(); }

    public function findById(int $id): ?Model { return $this->next->findById($id); }
    public function create(array $data): Model { return $this->next->create($data); }
    public function update(Model $model, array $data): bool { return $this->next->update($model, $data); }
    public function delete(Model $model): bool { return $this->next->delete($model); }
public function getPublishedArticlesSince(int $days): array
{
    return Cache::tags(['dashboard'])->remember("published_since_{$days}", 600, function () use ($days) {
        return $this->next->getPublishedArticlesSince($days);
    });
}
    }