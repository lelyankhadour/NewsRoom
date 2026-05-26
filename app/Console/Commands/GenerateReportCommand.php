<?php
namespace App\Console\Commands;

use App\Services\Reports\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateReportCommand extends Command
{
    protected $signature = 'articles:report {reportName} {--dry-run}';
    protected $description = 'Generate and log system reports';


public function handle(ReportService $reportService)
{
    $reportName = $this->argument('reportName');
    $report = $reportService->generateReport($reportName);
    $data = $report['data'];
    $this->info("Report: " . $report['report_type']);
    $tableData = [];
    foreach ($data as $key => $value) {
        $tableData[] = [
            $key, 
            is_array($value) ? json_encode($value) : $value
        ];
    }

    $this->table(['Metric', 'Value'], $tableData);
}

//  for Desingn Terminal
private function formatForTable($data) {
    $rows = [];
    foreach ($data as $key => $value) {
        $rows[] = [$key, $value];
    }
    return $rows;
}
}