<?php

namespace App\Services;

use App\Contracts\ReportRepositoryInterface;
use App\Jobs\GenerateWeeklyReportJob;
use App\Services\Reports\ReportService;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    /**
     * @param ReportRepositoryInterface $repository  
     * @param ReportService $reportService    
     */
    public function __construct(
        protected ReportRepositoryInterface $repository,
        protected ReportService $reportService
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'stats'        => $this->repository->getDashboardStats(),
            'generated_at' => now()->toDateTimeString(),
        ];
    }


public function triggerWeeklyReport()
{
   Log::info('Weekly report generation  by sevice.');
   GenerateWeeklyReportJob::dispatch();

 
}
}