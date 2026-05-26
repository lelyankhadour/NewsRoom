<?php

namespace App\Contracts;

interface ReportRepositoryInterface extends RepositoryInterface
{
    
    /**
     * Get total published articles within a specific days range.
     */
    public function getPublishedArticlesCount(int $days): int;

    /**
     * Get a list of top active writers based on article counts.
     */
    public function getTopWriters(int $limit = 5): array;
    public function getTotalCommentsCount(): int;
    public function getPublishedArticlesSince(int $days): array;
}