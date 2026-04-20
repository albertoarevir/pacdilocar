<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Services\OperationalSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function __construct(
        private readonly OperationalSummaryService $servicio
    ) {}

    public function index(Request $request): JsonResponse
    {
        $consulta = MaintenanceRecord::with([
            'vehiculo:id,patente',
            'categoriaMantenimiento:id,nombre',
            'taller:id,nombre,tipo',
        ]);

        if ($request->filled('vehiculo_id')) {
            $consulta->where('vehiculo_id', $request->vehiculo_id);
        }

        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        if ($request->filled('tipo_mantenimiento')) {
            $consulta->where('tipo_mantenimiento', $request->tipo_mantenimiento);
        }

        if ($request->filled('categoria_id')) {
            $consulta->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('desde')) {
            $consulta->whereDate('fecha_ingreso', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $consulta->whereDate('fecha_ingreso', '<=', $request->hasta);
        }

        $registros = $consulta->orderByDesc('fecha_ingreso')
            ->paginate($request->integer('per_page', 25));

        return response()->json($registros);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'vehiculo_id'          => 'required|exists:vehicles,id',
            'categoria_id'         => 'nullable|exists:maintenance_categories,id',
            'taller_id'            => 'nullable|exists:workshops,id',
            'fecha_ingreso'        => 'required|date',
            'fecha_salida'         => 'nullable|date|after_or_equal:fecha_ingreso',
            'estado'               => ['required', Rule::in(['Abierto', 'Cerrado', 'En Diagnóstico'])],
            'tipo_mantenimiento'   => ['required', Rule::in(['Correctivo', 'Preventivo', 'Emergencia'])],
            'descripcion_tecnica'  => 'nullable|string',
            'costo_total'          => 'nullable|numeric|min:0',
            'kilometraje_ingreso'  => 'nullable|integer|min:0',
            'numero_orden'         => 'nullable|string|max:50|unique:maintenance_records,numero_orden',
            'observaciones'        => 'nullable|string',
        ]);

        $registro = MaintenanceRecord::create($datos);

        $this->servicio->refresh($registro->vehiculo_id);

        return response()->json(
            $registro->load('vehiculo', 'categoriaMantenimiento', 'taller'),
            201
        );
    }

    public function show(MaintenanceRecord $maintenanceRecord): JsonResponse
    {
        return response()->json(
            $maintenanceRecord->load('vehiculo', 'categoriaMantenimiento', 'taller')
        );
    }

    public function update(Request $request, MaintenanceRecord $maintenanceRecord): JsonResponse
    {
        $datos = $request->validate([
            'categoria_id'        => 'nullable|exists:maintenance_categories,id',
            'taller_id'           => 'nullable|exists:workshops,id',
            'fecha_ingreso'       => 'sometimes|date',
            'fecha_salida'        => 'nullable|date|after_or_equal:fecha_ingreso',
            'estado'              => ['sometimes', Rule::in(['Abierto', 'Cerrado', 'En Diagnóstico'])],
            'tipo_mantenimiento'  => ['sometimes', Rule::in(['Correctivo', 'Preventivo', 'Emergencia'])],
            'descripcion_tecnica' => 'nullable|string',
            'costo_total'         => 'nullable|numeric|min:0',
            'kilometraje_ingreso' => 'nullable|integer|min:0',
            'numero_orden'        => [
                'nullable', 'string', 'max:50',
                Rule::unique('maintenance_records')->ignore($maintenanceRecord->id),
            ],
            'observaciones'       => 'nullable|string',
        ]);

        $maintenanceRecord->update($datos);

        $this->servicio->refresh($maintenanceRecord->vehiculo_id);

        return response()->json(
            $maintenanceRecord->fresh('vehiculo', 'categoriaMantenimiento', 'taller')
        );
    }

    public function destroy(MaintenanceRecord $maintenanceRecord): JsonResponse
    {
        $vehiculoId = $maintenanceRecord->vehiculo_id;
        $maintenanceRecord->delete();
        $this->servicio->refresh($vehiculoId);

        return response()->json(['mensaje' => 'Registro eliminado correctamente.']);
    }
}
