<?php
namespace App\Services\Reports;

use App\Contracts\ReportGeneratorInterface;
use App\Contracts\ReportRepositoryInterface;

class WeeklyArticlesReport implements ReportGeneratorInterface
{
    protected ReportRepositoryInterface $repository;

    public function __construct(ReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function generate(): array
    {    
        $articlesCount= $this->repository->getPublishedArticlesSince(7);
        // dd($articlesCount);
        return [
        'report_name' => $this->getReportName(),
        'period_days' => 7,
        'data'        => $articlesCount, 
        'generated_at'=> now()->toDateTimeString(),
    ];
    }

    public function getReportName(): string
    {
        return 'weekly_articles_report';
    }
}