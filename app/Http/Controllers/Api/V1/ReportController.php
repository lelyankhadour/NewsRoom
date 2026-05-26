<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Services\Reports\ReportService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class ReportController extends Controller
{
    use ApiResponse;

    protected \App\Services\Reports\ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
        
    }

    /**
     * Route API Endpoint for compiling dynamic reporting dashboards.
     */
    public function generate(Request $request): JsonResponse
    {
        try {
            $reportType = $request->query('type');

            if (!$reportType) {
                return $this->errorResponse("The reporting specification [type] parameter is missing from the request query string.", 400);
            }

            // Fire the Strategy Engine mapped to the specified contract implementation
            $reportData = $this->reportService->generateReport((string) $reportType);

            return $this->successResponse($reportData, "System analytics telemetry compiled successfully.");

        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            return $this->errorResponse("An internal anomaly blocked report compilation procedures.", 500);
        }
    }
}