<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardWebController extends Controller
{
    public function __construct(private readonly DashboardService $service) {}

    public function index(): View
    {
        return view('dashboard.index', ['stats' => $this->service->getSummary()]);
    }
}
