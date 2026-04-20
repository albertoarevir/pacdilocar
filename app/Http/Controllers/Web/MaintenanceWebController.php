<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = MaintenanceRecord::with(['vehicle:id,patente', 'maintenanceCategory:id,name', 'workshop:id,name']);

        if ($request->filled('record_status')) {
            $query->where('record_status', $request->record_status);
        }

        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('entry_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('entry_date', '<=', $request->to_date);
        }

        $records = $query->orderByDesc('entry_date')->paginate(25);

        return view('maintenance.index', compact('records'));
    }
}
