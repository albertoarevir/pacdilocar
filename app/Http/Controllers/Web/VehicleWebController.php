<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\FuelType;
use App\Models\FundingOrigin;
use App\Models\Municipality;
use App\Models\Prefecture;
use App\Models\Unit;
use App\Models\Vehicle;
use App\Models\VehicleFunction;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use App\Models\Zone;
use App\Services\OperationalSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehicleWebController extends Controller
{
    public function __construct(
        private readonly OperationalSummaryService $summaryService
    ) {}

    public function index(Request $request): View
    {
        $query = Vehicle::with(['vehicleType', 'brand', 'vehicleModel', 'vehicleStatus', 'zone', 'prefecture', 'operationalSummary']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('patente', 'like', "%{$request->search}%")
                  ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('vehicleModel', fn($m) => $m->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('vehicleStatus', fn($q) => $q->where('code', $request->status));
        }

        $vehicles = $query->orderBy('patente')->paginate(20);
        $statuses = VehicleStatus::orderBy('sort_order')->get();

        return view('vehicles.index', compact('vehicles', 'statuses'));
    }

    /** Verifica en tiempo real si una patente ya existe en la BD. */
    public function checkPatente(Request $request): JsonResponse
    {
        $patente = strtoupper(trim($request->query('patente', '')));

        if (strlen($patente) < 2) {
            return response()->json(['available' => null]);
        }

        $exists = Vehicle::where('patente', $patente)->exists();

        return response()->json([
            'available' => ! $exists,
            'patente'   => $patente,
            'message'   => $exists
                ? "La patente/sigla «{$patente}» ya está registrada en la base de datos."
                : "Patente/sigla disponible.",
        ]);
    }

    public function create(): View
    {
        $brands = Brand::with('vehicleModels:id,brand_id,name')
            ->orderBy('name')
            ->get(['id', 'name']);

        $brandsModels = $brands->mapWithKeys(fn($b) => [
            $b->name => $b->vehicleModels->pluck('name')->toArray()
        ])->toArray();

        // Para el JS de cascada también necesitamos los IDs
        $brandsWithIds = $brands->map(fn($b) => [
            'id'     => $b->id,
            'name'   => $b->name,
            'models' => $b->vehicleModels->map(fn($m) => ['id' => $m->id, 'name' => $m->name])->toArray(),
        ])->toArray();

        return view('vehicles.create', [
            'vehicleTypes'   => VehicleType::orderBy('code')->get(),
            'colors'         => Color::orderBy('name')->get(),
            'fuelTypes'      => FuelType::orderBy('name')->get(),
            'fundingOrigins' => FundingOrigin::orderBy('name')->get(),
            'zones'          => Zone::orderBy('name')->get(),
            'prefectures'    => Prefecture::orderBy('name')->get(),
            'units'          => Unit::orderBy('name')->get(),
            'statuses'       => VehicleStatus::orderBy('sort_order')->get(),
            'functions'      => VehicleFunction::orderBy('name')->get(),
            'brandsModels'   => $brandsModels,
            'brandsWithIds'  => $brandsWithIds,
            'geoCascade'     => self::buildGeoCascade(),
        ]);
    }

    private static function buildGeoCascade(): array
    {
        $zones          = Zone::orderBy('name')->get()->keyBy('id');
        $municipalities = Municipality::with('province')
            ->whereNotNull('zone_id')
            ->orderBy('name')
            ->get();

        $cascade = [];
        foreach ($municipalities as $m) {
            $zid = $m->zone_id;
            $pid = $m->province_id;

            if (! isset($cascade[$zid])) {
                $cascade[$zid] = [
                    'name'      => $zones[$zid]->name ?? '',
                    'provinces' => [],
                ];
            }
            if (! isset($cascade[$zid]['provinces'][$pid])) {
                $cascade[$zid]['provinces'][$pid] = [
                    'name'           => $m->province->name,
                    'municipalities' => [],
                ];
            }
            $cascade[$zid]['provinces'][$pid]['municipalities'][] = [
                'id'   => $m->id,
                'name' => $m->name,
            ];
        }

        return $cascade;
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patente'            => 'required|string|max:20|unique:vehicles,patente',
            'vehicle_type_id'    => 'required|exists:vehicle_types,id',
            'brand_id'           => 'nullable|exists:brands,id',
            'vehicle_model_id'   => 'nullable|exists:vehicle_models,id',
            'color_id'           => 'nullable|exists:colors,id',
            'year'               => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'service_start_date' => 'nullable|date',
            'vehicle_function_id'=> 'nullable|exists:vehicle_functions,id',
            'fuel_type_id'       => 'nullable|exists:fuel_types,id',
            'engine_number'      => 'nullable|string|max:100',
            'chassis_number'     => 'nullable|string|max:100',
            'funding_origin_id'  => 'nullable|exists:funding_origins,id',
            'zone_id'            => 'nullable|exists:zones,id',
            'province_id'        => 'nullable|exists:provinces,id',
            'municipality_id'    => 'nullable|exists:municipalities,id',
            'prefecture_id'      => 'nullable|exists:prefectures,id',
            'unit_id'            => 'nullable|exists:units,id',
            'is_aggregated'      => 'boolean',
            'aggregate_prefecture_id' => 'nullable|exists:prefectures,id',
            'aggregate_unit_id'       => 'nullable|exists:units,id',
            'vehicle_status_id'  => ['required', 'exists:vehicle_statuses,id'],
            'observations'       => 'nullable|string|max:1000',
        ], [
            'patente.required'          => 'La patente o sigla es obligatoria.',
            'patente.unique'            => 'Ya existe un vehículo con esa patente.',
            'patente.max'               => 'La patente no puede superar 20 caracteres.',
            'vehicle_type_id.required'  => 'Debe seleccionar el tipo de vehículo.',
            'vehicle_type_id.exists'    => 'El tipo de vehículo seleccionado no es válido.',
            'year.integer'              => 'El año debe ser un número entero.',
            'year.min'                  => 'El año no puede ser anterior a 1950.',
            'year.max'                  => 'El año no puede ser mayor al año siguiente.',
            'service_start_date.date'   => 'La fecha de alta debe ser una fecha válida.',
            'vehicle_status_id.required'=> 'Debe seleccionar el estado del vehículo.',
        ]);

        $data['is_aggregated'] = $request->boolean('is_aggregated');

        $vehicle = Vehicle::create($data);
        $this->summaryService->refresh($vehicle->id);

        return redirect()->route('vehicles.index')
            ->with('success', "Vehículo {$vehicle->patente} registrado correctamente.");
    }

    public function edit(Vehicle $vehicle): View
    {
        $brands = Brand::with('vehicleModels:id,brand_id,name')
            ->orderBy('name')
            ->get(['id', 'name']);

        $brandsWithIds = $brands->map(fn($b) => [
            'id'     => $b->id,
            'name'   => $b->name,
            'models' => $b->vehicleModels->map(fn($m) => ['id' => $m->id, 'name' => $m->name])->toArray(),
        ])->toArray();

        return view('vehicles.edit', [
            'vehicle'        => $vehicle,
            'vehicleTypes'   => VehicleType::orderBy('code')->get(),
            'colors'         => Color::orderBy('name')->get(),
            'fuelTypes'      => FuelType::orderBy('name')->get(),
            'fundingOrigins' => FundingOrigin::orderBy('name')->get(),
            'zones'          => Zone::orderBy('name')->get(),
            'prefectures'    => Prefecture::orderBy('name')->get(),
            'units'          => Unit::orderBy('name')->get(),
            'statuses'       => VehicleStatus::orderBy('sort_order')->get(),
            'functions'      => VehicleFunction::orderBy('name')->get(),
            'brandsWithIds'  => $brandsWithIds,
            'geoCascade'     => self::buildGeoCascade(),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $request->validate([
            'patente'             => ['required','string','max:20', Rule::unique('vehicles','patente')->ignore($vehicle->id)],
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
            'observations'        => 'nullable|string|max:1000',
        ], [
            'patente.required'          => 'La patente o sigla es obligatoria.',
            'patente.unique'            => 'Ya existe otro vehículo con esa patente.',
            'patente.max'               => 'La patente no puede superar 20 caracteres.',
            'vehicle_type_id.required'  => 'Debe seleccionar el tipo de vehículo.',
            'year.integer'              => 'El año debe ser un número entero.',
            'year.min'                  => 'El año no puede ser anterior a 1950.',
            'year.max'                  => 'El año no puede ser mayor al año siguiente.',
            'service_start_date.date'   => 'La fecha de alta debe ser una fecha válida.',
            'vehicle_status_id.required'=> 'Debe seleccionar el estado del vehículo.',
        ]);

        $data['is_aggregated'] = $request->boolean('is_aggregated');

        $vehicle->update($data);
        $this->summaryService->refresh($vehicle->id);

        return redirect()->route('vehicles.index')
            ->with('success', "Vehículo {$vehicle->patente} actualizado correctamente.");
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load([
            'vehicleType', 'brand', 'vehicleModel', 'vehicleStatus', 'vehicleFunction',
            'color', 'fuelType', 'fundingOrigin',
            'zone', 'province', 'municipality', 'prefecture', 'unit',
            'aggregatePrefecture', 'aggregateUnit', 'operationalSummary',
            'maintenanceRecords.maintenanceCategory',
            'maintenanceRecords.workshop',
        ]);

        return view('vehicles.show', compact('vehicle'));
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $patente = $vehicle->patente;
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', "Vehículo {$patente} eliminado correctamente.");
    }
}
