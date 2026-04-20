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
        private readonly OperationalSummaryService $servicio
    ) {}

    public function index(Request $request): View
    {
        $consulta = Vehicle::with(['tipoVehiculo', 'marca', 'modelo', 'estadoVehiculo', 'zona', 'prefectura', 'resumenOperativo']);

        if ($request->filled('search')) {
            $consulta->where(function ($q) use ($request) {
                $q->where('patente', 'like', "%{$request->search}%")
                  ->orWhereHas('marca', fn($b) => $b->where('nombre', 'like', "%{$request->search}%"))
                  ->orWhereHas('modelo', fn($m) => $m->where('nombre', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('status')) {
            $consulta->whereHas('estadoVehiculo', fn($q) => $q->where('codigo', $request->status));
        }

        $vehiculos = $consulta->orderBy('patente')->paginate(20);
        $estados   = VehicleStatus::orderBy('orden')->get();

        return view('vehicles.index', compact('vehiculos', 'estados'));
    }

    /** Verifica en tiempo real si una patente ya existe en la BD. */
    public function checkPatente(Request $request): JsonResponse
    {
        $patente = strtoupper(trim($request->query('patente', '')));

        if (strlen($patente) < 2) {
            return response()->json(['disponible' => null]);
        }

        $existe = Vehicle::where('patente', $patente)->exists();

        return response()->json([
            'disponible' => ! $existe,
            'patente'    => $patente,
            'mensaje'    => $existe
                ? "La patente/sigla «{$patente}» ya está registrada en la base de datos."
                : "Patente/sigla disponible.",
        ]);
    }

    public function create(): View
    {
        $marcas = Brand::with('modelos:id,marca_id,nombre')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $marcasModelos = $marcas->mapWithKeys(fn($m) => [
            $m->nombre => $m->modelos->pluck('nombre')->toArray()
        ])->toArray();

        $marcasConIds = $marcas->map(fn($m) => [
            'id'      => $m->id,
            'nombre'  => $m->nombre,
            'modelos' => $m->modelos->map(fn($mo) => ['id' => $mo->id, 'nombre' => $mo->nombre])->toArray(),
        ])->toArray();

        return view('vehicles.create', [
            'tiposVehiculo'       => VehicleType::orderBy('codigo')->get(),
            'colores'             => Color::orderBy('nombre')->get(),
            'tiposCombustible'    => FuelType::orderBy('nombre')->get(),
            'origenesFinanciamiento' => FundingOrigin::orderBy('nombre')->get(),
            'zonas'               => Zone::orderBy('nombre')->get(),
            'prefecturas'         => Prefecture::orderBy('nombre')->get(),
            'unidades'            => Unit::orderBy('nombre')->get(),
            'estados'             => VehicleStatus::orderBy('orden')->get(),
            'funciones'           => VehicleFunction::orderBy('nombre')->get(),
            'marcasModelos'       => $marcasModelos,
            'marcasConIds'        => $marcasConIds,
            'geoCascada'          => self::construirGeoCascada(),
        ]);
    }

    private static function construirGeoCascada(): array
    {
        $zonas     = Zone::orderBy('nombre')->get()->keyBy('id');
        $municipios = Municipality::with('provincia')
            ->whereNotNull('zona_id')
            ->orderBy('nombre')
            ->get();

        $cascada = [];
        foreach ($municipios as $municipio) {
            $zonaId     = $municipio->zona_id;
            $provinciaId = $municipio->province_id;

            if (! isset($cascada[$zonaId])) {
                $cascada[$zonaId] = [
                    'nombre'    => $zonas[$zonaId]->nombre ?? '',
                    'provincias' => [],
                ];
            }
            if (! isset($cascada[$zonaId]['provincias'][$provinciaId])) {
                $cascada[$zonaId]['provincias'][$provinciaId] = [
                    'nombre'     => $municipio->provincia->nombre,
                    'municipios' => [],
                ];
            }
            $cascada[$zonaId]['provincias'][$provinciaId]['municipios'][] = [
                'id'     => $municipio->id,
                'nombre' => $municipio->nombre,
            ];
        }

        return $cascada;
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'patente'                 => 'required|string|max:20|unique:vehicles,patente',
            'tipo_vehiculo_id'        => 'required|exists:vehicle_types,id',
            'marca_id'                => 'nullable|exists:brands,id',
            'modelo_id'               => 'nullable|exists:vehicle_models,id',
            'color_id'                => 'nullable|exists:colors,id',
            'anio'                    => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'fecha_inicio_servicio'   => 'nullable|date',
            'funcion_id'              => 'nullable|exists:vehicle_functions,id',
            'tipo_combustible_id'     => 'nullable|exists:fuel_types,id',
            'numero_motor'            => 'nullable|string|max:100',
            'numero_chasis'           => 'nullable|string|max:100',
            'origen_financiamiento_id' => 'nullable|exists:funding_origins,id',
            'zona_id'                 => 'nullable|exists:zones,id',
            'province_id'             => 'nullable|exists:provinces,id',
            'municipio_id'            => 'nullable|exists:municipalities,id',
            'prefectura_id'           => 'nullable|exists:prefectures,id',
            'unidad_id'               => 'nullable|exists:units,id',
            'es_agregado'             => 'boolean',
            'prefectura_agregado_id'  => 'nullable|exists:prefectures,id',
            'unidad_agregado_id'      => 'nullable|exists:units,id',
            'estado_vehiculo_id'      => ['required', 'exists:vehicle_statuses,id'],
            'observaciones'           => 'nullable|string|max:1000',
        ], [
            'patente.required'               => 'La patente o sigla es obligatoria.',
            'patente.unique'                 => 'Ya existe un vehículo con esa patente.',
            'patente.max'                    => 'La patente no puede superar 20 caracteres.',
            'tipo_vehiculo_id.required'      => 'Debe seleccionar el tipo de vehículo.',
            'tipo_vehiculo_id.exists'        => 'El tipo de vehículo seleccionado no es válido.',
            'anio.integer'                   => 'El año debe ser un número entero.',
            'anio.min'                       => 'El año no puede ser anterior a 1950.',
            'anio.max'                       => 'El año no puede ser mayor al año siguiente.',
            'fecha_inicio_servicio.date'     => 'La fecha de alta debe ser una fecha válida.',
            'estado_vehiculo_id.required'    => 'Debe seleccionar el estado del vehículo.',
        ]);

        $datos['es_agregado'] = $request->boolean('es_agregado');

        $vehiculo = Vehicle::create($datos);
        $this->servicio->refresh($vehiculo->id);

        return redirect()->route('vehicles.index')
            ->with('success', "Vehículo {$vehiculo->patente} registrado correctamente.");
    }

    public function edit(Vehicle $vehicle): View
    {
        $marcas = Brand::with('modelos:id,marca_id,nombre')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $marcasConIds = $marcas->map(fn($m) => [
            'id'      => $m->id,
            'nombre'  => $m->nombre,
            'modelos' => $m->modelos->map(fn($mo) => ['id' => $mo->id, 'nombre' => $mo->nombre])->toArray(),
        ])->toArray();

        return view('vehicles.edit', [
            'vehiculo'            => $vehicle,
            'tiposVehiculo'       => VehicleType::orderBy('codigo')->get(),
            'colores'             => Color::orderBy('nombre')->get(),
            'tiposCombustible'    => FuelType::orderBy('nombre')->get(),
            'origenesFinanciamiento' => FundingOrigin::orderBy('nombre')->get(),
            'zonas'               => Zone::orderBy('nombre')->get(),
            'prefecturas'         => Prefecture::orderBy('nombre')->get(),
            'unidades'            => Unit::orderBy('nombre')->get(),
            'estados'             => VehicleStatus::orderBy('orden')->get(),
            'funciones'           => VehicleFunction::orderBy('nombre')->get(),
            'marcasConIds'        => $marcasConIds,
            'geoCascada'          => self::construirGeoCascada(),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $datos = $request->validate([
            'patente'                 => ['required','string','max:20', Rule::unique('vehicles','patente')->ignore($vehicle->id)],
            'tipo_vehiculo_id'        => 'required|exists:vehicle_types,id',
            'marca_id'                => 'nullable|exists:brands,id',
            'modelo_id'               => 'nullable|exists:vehicle_models,id',
            'color_id'                => 'nullable|exists:colors,id',
            'anio'                    => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'fecha_inicio_servicio'   => 'nullable|date',
            'funcion_id'              => 'nullable|exists:vehicle_functions,id',
            'tipo_combustible_id'     => 'nullable|exists:fuel_types,id',
            'numero_motor'            => 'nullable|string|max:100',
            'numero_chasis'           => 'nullable|string|max:100',
            'origen_financiamiento_id' => 'nullable|exists:funding_origins,id',
            'zona_id'                 => 'nullable|exists:zones,id',
            'province_id'             => 'nullable|exists:provinces,id',
            'municipio_id'            => 'nullable|exists:municipalities,id',
            'prefectura_id'           => 'nullable|exists:prefectures,id',
            'unidad_id'               => 'nullable|exists:units,id',
            'es_agregado'             => 'boolean',
            'prefectura_agregado_id'  => 'nullable|exists:prefectures,id',
            'unidad_agregado_id'      => 'nullable|exists:units,id',
            'estado_vehiculo_id'      => ['required', 'exists:vehicle_statuses,id'],
            'observaciones'           => 'nullable|string|max:1000',
        ], [
            'patente.required'            => 'La patente o sigla es obligatoria.',
            'patente.unique'              => 'Ya existe otro vehículo con esa patente.',
            'patente.max'                 => 'La patente no puede superar 20 caracteres.',
            'tipo_vehiculo_id.required'   => 'Debe seleccionar el tipo de vehículo.',
            'anio.integer'                => 'El año debe ser un número entero.',
            'anio.min'                    => 'El año no puede ser anterior a 1950.',
            'anio.max'                    => 'El año no puede ser mayor al año siguiente.',
            'fecha_inicio_servicio.date'  => 'La fecha de alta debe ser una fecha válida.',
            'estado_vehiculo_id.required' => 'Debe seleccionar el estado del vehículo.',
        ]);

        $datos['es_agregado'] = $request->boolean('es_agregado');

        $vehicle->update($datos);
        $this->servicio->refresh($vehicle->id);

        return redirect()->route('vehicles.index')
            ->with('success', "Vehículo {$vehicle->patente} actualizado correctamente.");
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load([
            'tipoVehiculo', 'marca', 'modelo', 'estadoVehiculo', 'funcion',
            'color', 'tipoCombustible', 'origenFinanciamiento',
            'zona', 'provincia', 'municipio', 'prefectura', 'unidad',
            'prefecturaAgregado', 'unidadAgregado', 'resumenOperativo',
            'registrosMantenimiento.categoriaMantenimiento',
            'registrosMantenimiento.taller',
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
