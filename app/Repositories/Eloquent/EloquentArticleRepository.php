<?php

namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Contracts\ArticleRepositoryInterface;
use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentArticleRepository implements ArticleRepositoryInterface
{
    protected Article $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    public function getAllPublished(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('status', ArticleStatus::PUBLISHED->value)
            ->with(['user', 'tags'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function syncTags(Article $article, array $tags): array
    {
        return $article->tags()->sync($tags);
    }

    public function detachTags(Article $article): void
    {
        $article->tags()->detach();
    }
}