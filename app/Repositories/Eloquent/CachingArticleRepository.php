<?php

namespace App\Repositories\Eloquent;

use App\Contracts\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CachingArticleRepository implements ArticleRepositoryInterface
{
    protected ArticleRepositoryInterface $next;
    protected const CACHE_TTL = 3600;
    protected const TAG_LIST = 'articles.list';
    protected const TAG_SINGLE = 'articles.single';

    public function __construct(ArticleRepositoryInterface $next)
    {
        $this->next = $next;
    }

 
public function getAllPublished(int $perPage = 15): LengthAwarePaginator
{
    $page = request()->input('page', 1);
    $cacheKey = "published.per_page.{$perPage}.page.{$page}";
    $tags = [self::TAG_LIST];

    $cachedData = Cache::tags($tags)->get($cacheKey);
    
    if (is_null($cachedData)) {
        \Log::info("Cache miss for key: {$cacheKey}");
        $data = $this->next->getAllPublished($perPage);
        Cache::tags($tags)->put($cacheKey, $data, self::CACHE_TTL);
        return $data;
    }

    \Log::info("Cache hit for key: {$cacheKey}");
    return $cachedData;
}

    public function findById(int $id): ?Model
    {
        return Cache::tags([self::TAG_SINGLE])->remember("id.{$id}", self::CACHE_TTL, function () use ($id) {
            return $this->next->findById($id);
        });
    }

    public function create(array $data): Model
    {
        $article = $this->next->create($data);
        $this->invalidate();
        return $article;
    }

    public function update(Model $model, array $data): bool
    {
        $updated = $this->next->update($model, $data);
        if ($updated) {

            Cache::tags([self::TAG_SINGLE])->forget("id.{$model->id}");
            $this->invalidate();
        }
        // dd( $updated);
        return $updated;
    }

    public function delete(Model $model): bool
    {
        $deleted = $this->next->delete($model);
        if ($deleted) {
            Cache::tags([self::TAG_SINGLE])->forget("id.{$model->id}");
            $this->invalidate();
        }
        return $deleted;
    }

    public function syncTags(Article $article, array $tags): array
    {
        $result = $this->next->syncTags($article, $tags);
        Cache::tags([self::TAG_SINGLE])->forget("id.{$article->id}");
        $this->invalidate();
        return $result;
    }

    public function detachTags(Article $article): void
{

    $this->next->detachTags($article);

        Cache::tags([self::TAG_SINGLE])->forget("id.{$article->id}");

    $this->invalidate();
}
    
    protected function invalidate(): void
    {
        Cache::tags([self::TAG_LIST])->flush();
    }
}