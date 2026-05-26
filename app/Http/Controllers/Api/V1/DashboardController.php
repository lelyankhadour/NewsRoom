<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ApiResponse;

    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;

        // Strict Admin Authentication Guarding Line
        // $this->middleware(['auth:sanctum', 'can:access-dashboard']);
    }

    /**
     * Fetch primary statistical metrics cards for the main layout.
     */
public function index(): JsonResponse
{
    try {
        // بدلاً من السناب شوت الجزئي، نجلب الـ Dashboard الكامل
        $stats = $this->dashboardService->getDashboardStats();
        
        return $this->successResponse($stats, "Dashboard metrics retrieved successfully.");
    } catch (\Throwable $exception) {
        return $this->errorResponse("Failed to load dashboard metrics.", 500);
    }
}
public function triggerReport(): JsonResponse
    {
        try {
           $this->dashboardService->triggerWeeklyReport();
            return $this->successResponse( "Weekly report generation ");
        } catch (\Throwable $exception) {
            return $this->errorResponse("Failed to trigger report generation.", 500);
        }
    }
}