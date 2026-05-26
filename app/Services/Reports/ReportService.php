<?php

namespace App\Services\Reports;

use App\Contracts\ReportGeneratorInterface;
use InvalidArgumentException;

class ReportService
{
    /**
     * @var array<string, ReportGeneratorInterface>
     */
    protected array $generators = [];

    /**
     * Inject all available report strategies dynamic collection.
     */
    public function __construct(array $generators)
    {
        foreach ($generators as $generator) {
            if ($generator instanceof ReportGeneratorInterface) {
                $this->generators[$generator->getReportName()] = $generator;
            }
        }
    }

  
    public function generateReport(string $reportName): array
{
    if (!isset($this->generators[$reportName])) {

               $availableReports = array_keys($this->generators);
        
        throw new InvalidArgumentException(
            "The requested report identifier [{$reportName}] is not registered. Available reports are: " . implode(', ', $availableReports)
        );
    }

    return [
        'report_type'  => $reportName,
        'generated_at' => now()->toDateTimeString(),
        'data'         => $this->generators[$reportName]->generate(),
    ];
}
}