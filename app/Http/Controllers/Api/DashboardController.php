<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\OperationalSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly OperationalSummaryService $summaryService,
    ) {}

    public function summary(): JsonResponse
    {
        return response()->json($this->dashboardService->getSummary());
    }

    public function fleetStatus(): JsonResponse
    {
        return response()->json($this->dashboardService->getFleetStatusBreakdown());
    }

    public function refreshAllSummaries(): JsonResponse
    {
        $this->summaryService->refreshAll();

        return response()->json(['message' => 'Indicadores operativos recalculados correctamente.']);
    }
}
