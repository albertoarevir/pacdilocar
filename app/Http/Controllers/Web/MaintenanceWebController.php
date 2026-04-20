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
        $consulta = MaintenanceRecord::with([
            'vehiculo:id,patente',
            'categoriaMantenimiento:id,nombre',
            'taller:id,nombre',
        ]);

        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        if ($request->filled('tipo_mantenimiento')) {
            $consulta->where('tipo_mantenimiento', $request->tipo_mantenimiento);
        }

        if ($request->filled('desde')) {
            $consulta->whereDate('fecha_ingreso', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $consulta->whereDate('fecha_ingreso', '<=', $request->hasta);
        }

        $registros = $consulta->orderByDesc('fecha_ingreso')->paginate(25);

        return view('maintenance.index', compact('registros'));
    }
}
