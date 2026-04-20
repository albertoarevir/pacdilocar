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
        private readonly OperationalSummaryService $summaryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = MaintenanceRecord::with([
            'vehicle:id,patente,status',
            'maintenanceCategory:id,name',
            'workshop:id,name,type',
        ]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('record_status')) {
            $query->where('record_status', $request->record_status);
        }

        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        if ($request->filled('category_id')) {
            $query->where('maintenance_category_id', $request->category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('entry_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('entry_date', '<=', $request->to_date);
        }

        $records = $query->orderByDesc('entry_date')
            ->paginate($request->integer('per_page', 25));

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'vehicle_id'              => 'required|exists:vehicles,id',
            'maintenance_category_id' => 'nullable|exists:maintenance_categories,id',
            'workshop_id'             => 'nullable|exists:workshops,id',
            'entry_date'              => 'required|date',
            'exit_date'               => 'nullable|date|after_or_equal:entry_date',
            'record_status'           => ['required', Rule::in(['Abierto', 'Cerrado', 'En Diagnóstico'])],
            'maintenance_type'        => ['required', Rule::in(['Correctivo', 'Preventivo', 'Emergencia'])],
            'technical_description'   => 'nullable|string',
            'total_cost'              => 'nullable|numeric|min:0',
            'mileage_entry'           => 'nullable|integer|min:0',
            'work_order_number'       => 'nullable|string|max:50|unique:maintenance_records,work_order_number',
            'observations'            => 'nullable|string',
        ]);

        $record = MaintenanceRecord::create($data);

        // Actualiza estado del vehículo si el registro queda abierto
        if (in_array($record->record_status, ['Abierto', 'En Diagnóstico'])) {
            Vehicle::where('id', $record->vehicle_id)
                ->where('status', 'OPERATIVO')
                ->update(['status' => 'MANTENIMIENTO']);
        }

        $this->summaryService->refresh($record->vehicle_id);

        return response()->json($record->load('vehicle', 'maintenanceCategory', 'workshop'), 201);
    }

    public function show(MaintenanceRecord $maintenanceRecord): JsonResponse
    {
        return response()->json(
            $maintenanceRecord->load('vehicle', 'maintenanceCategory', 'workshop')
        );
    }

    public function update(Request $request, MaintenanceRecord $maintenanceRecord): JsonResponse
    {
        $data = $request->validate([
            'maintenance_category_id' => 'nullable|exists:maintenance_categories,id',
            'workshop_id'             => 'nullable|exists:workshops,id',
            'entry_date'              => 'sometimes|date',
            'exit_date'               => 'nullable|date|after_or_equal:entry_date',
            'record_status'           => ['sometimes', Rule::in(['Abierto', 'Cerrado', 'En Diagnóstico'])],
            'maintenance_type'        => ['sometimes', Rule::in(['Correctivo', 'Preventivo', 'Emergencia'])],
            'technical_description'   => 'nullable|string',
            'total_cost'              => 'nullable|numeric|min:0',
            'mileage_entry'           => 'nullable|integer|min:0',
            'work_order_number'       => [
                'nullable', 'string', 'max:50',
                Rule::unique('maintenance_records')->ignore($maintenanceRecord->id),
            ],
            'observations'            => 'nullable|string',
        ]);

        $maintenanceRecord->update($data);

        // Cuando se cierra el registro, libera el vehículo si no tiene otros abiertos
        if (isset($data['record_status']) && $data['record_status'] === 'Cerrado') {
            $stillOpen = MaintenanceRecord::where('vehicle_id', $maintenanceRecord->vehicle_id)
                ->whereIn('record_status', ['Abierto', 'En Diagnóstico'])
                ->where('id', '!=', $maintenanceRecord->id)
                ->exists();

            if (! $stillOpen) {
                Vehicle::where('id', $maintenanceRecord->vehicle_id)
                    ->whereIn('status', ['MANTENIMIENTO', 'PANNE'])
                    ->update(['status' => 'OPERATIVO']);
            }
        }

        $this->summaryService->refresh($maintenanceRecord->vehicle_id);

        return response()->json($maintenanceRecord->fresh('vehicle', 'maintenanceCategory', 'workshop'));
    }

    public function destroy(MaintenanceRecord $maintenanceRecord): JsonResponse
    {
        $vehicleId = $maintenanceRecord->vehicle_id;
        $maintenanceRecord->delete();
        $this->summaryService->refresh($vehicleId);

        return response()->json(['message' => 'Registro eliminado correctamente.']);
    }
}
