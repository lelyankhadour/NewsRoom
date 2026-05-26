<?php

namespace App\Contracts;

use App\Models\Article;


interface ArticleRepositoryInterface extends RepositoryInterface
{
    public function getAllPublished(int $perPage = 15): mixed;

    public function syncTags(Article $article, array $tags): array;

    public function detachTags(Article $article): void;
}