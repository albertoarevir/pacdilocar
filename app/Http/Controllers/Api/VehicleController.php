<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\OperationalSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function __construct(
        private readonly OperationalSummaryService $summaryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::with([
            'vehicleType:id,code,name',
            'brand:id,name',
            'vehicleModel:id,brand_id,name',
            'vehicleStatus:id,code,name',
            'vehicleFunction:id,name',
            'color:id,name',
            'fuelType:id,name',
            'fundingOrigin:id,name',
            'zone:id,name',
            'province:id,name',
            'municipality:id,name',
            'prefecture:id,name',
            'unit:id,name',
        ]);

        if ($request->filled('status')) {
            $query->whereHas('vehicleStatus', fn($q) => $q->where('code', $request->status));
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('prefecture_id')) {
            $query->where('prefecture_id', $request->prefecture_id);
        }

        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('patente', 'like', "%{$request->search}%")
                  ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('vehicleModel', fn($m) => $m->where('name', 'like', "%{$request->search}%"));
            });
        }

        $vehicles = $query->orderBy('patente')->paginate($request->integer('per_page', 25));

        return response()->json($vehicles);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patente'             => 'required|string|max:20|unique:vehicles,patente',
            'vehicle_type_id'     => 'required|exists:vehicle_types,id',
            'brand_id'            => 'nullable|exists:brands,id',
            'vehicle_model_id'    => 'nullable|exists:vehicle_models,id',
            'color_id'            => 'nullable|exists:colors,id',
            'year'                => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'service_start_date'  => 'nullable|date',
            'vehicle_function_id' => 'nullable|exists:vehicle_functions,id',
            'fuel_type_id'        => 'nullable|exists:fuel_types,id',
            'engine_number'       => 'nullable|string|max:100',
            'chassis_number'      => 'nullable|string|max:100',
            'funding_origin_id'   => 'nullable|exists:funding_origins,id',
            'zone_id'             => 'nullable|exists:zones,id',
            'province_id'         => 'nullable|exists:provinces,id',
            'municipality_id'     => 'nullable|exists:municipalities,id',
            'prefecture_id'       => 'nullable|exists:prefectures,id',
            'unit_id'             => 'nullable|exists:units,id',
            'is_aggregated'       => 'boolean',
            'aggregate_prefecture_id' => 'nullable|exists:prefectures,id',
            'aggregate_unit_id'       => 'nullable|exists:units,id',
            'vehicle_status_id'   => ['required', 'exists:vehicle_statuses,id'],
            'observations'        => 'nullable|string',
        ]);

        $vehicle = Vehicle::create($data);
        $this->summaryService->refresh($vehicle->id);

        return response()->json($vehicle->load('vehicleType', 'vehicleStatus', 'brand', 'vehicleModel', 'prefecture', 'unit'), 201);
    }

    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load([
            'vehicleType', 'brand', 'vehicleModel', 'vehicleStatus', 'vehicleFunction',
            'color', 'fuelType', 'fundingOrigin',
            'zone', 'province', 'municipality', 'prefecture', 'unit',
            'aggregatePrefecture', 'aggregateUnit',
            'operationalSummary',
            'maintenanceRecords' => fn ($q) => $q->with('maintenanceCategory', 'workshop')
                                                  ->orderByDesc('entry_date'),
        ]);

        return response()->json($vehicle);
    }

    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $data = $request->validate([
            'patente'             => ['sometimes', 'string', 'max:20', Rule::unique('vehicles')->ignore($vehicle->id)],
            'vehicle_type_id'     => 'sometimes|exists:vehicle_types,id',
            'brand_id'            => 'nullable|exists:brands,id',
            'vehicle_model_id'    => 'nullable|exists:vehicle_models,id',
            'color_id'            => 'nullable|exists:colors,id',
            'year'                => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'service_start_date'  => 'nullable|date',
            'vehicle_function_id' => 'nullable|exists:vehicle_functions,id',
            'fuel_type_id'        => 'nullable|exists:fuel_types,id',
            'engine_number'       => 'nullable|string|max:100',
            'chassis_number'      => 'nullable|string|max:100',
            'funding_origin_id'   => 'nullable|exists:funding_origins,id',
            'zone_id'             => 'nullable|exists:zones,id',
            'province_id'         => 'nullable|exists:provinces,id',
            'municipality_id'     => 'nullable|exists:municipalities,id',
            'prefecture_id'       => 'nullable|exists:prefectures,id',
            'unit_id'             => 'nullable|exists:units,id',
            'is_aggregated'       => 'boolean',
            'aggregate_prefecture_id' => 'nullable|exists:prefectures,id',
            'aggregate_unit_id'       => 'nullable|exists:units,id',
            'vehicle_status_id'   => ['sometimes', 'exists:vehicle_statuses,id'],
            'observations'        => 'nullable|string',
        ]);

        $vehicle->update($data);
        $this->summaryService->refresh($vehicle->id);

        return response()->json($vehicle->fresh(['vehicleType', 'vehicleStatus', 'brand', 'vehicleModel', 'operationalSummary']));
    }

    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $vehicle->delete();

        return response()->json(['message' => 'Vehículo eliminado correctamente.']);
    }

    public function operationalSummary(Vehicle $vehicle): JsonResponse
    {
        $summary = $this->summaryService->refresh($vehicle->id);

        return response()->json([
            'vehicle'  => $vehicle->only('id', 'patente', 'vehicle_status_id'),
            'summary'  => $summary,
            'computed' => $summary->last_computed_at,
        ]);
    }
}
