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
        private readonly OperationalSummaryService $servicio
    ) {}

    public function index(Request $request): JsonResponse
    {
        $consulta = Vehicle::with([
            'tipoVehiculo:id,codigo,nombre',
            'marca:id,nombre',
            'modelo:id,marca_id,nombre',
            'estadoVehiculo:id,codigo,nombre',
            'funcion:id,nombre',
            'color:id,nombre',
            'tipoCombustible:id,nombre',
            'origenFinanciamiento:id,nombre',
            'zona:id,nombre',
            'provincia:id,nombre',
            'municipio:id,nombre',
            'prefectura:id,nombre',
            'unidad:id,nombre',
        ]);

        if ($request->filled('status')) {
            $consulta->whereHas('estadoVehiculo', fn($q) => $q->where('codigo', $request->status));
        }

        if ($request->filled('zona_id')) {
            $consulta->where('zona_id', $request->zona_id);
        }

        if ($request->filled('prefectura_id')) {
            $consulta->where('prefectura_id', $request->prefectura_id);
        }

        if ($request->filled('tipo_vehiculo_id')) {
            $consulta->where('tipo_vehiculo_id', $request->tipo_vehiculo_id);
        }

        if ($request->filled('search')) {
            $consulta->where(function ($q) use ($request) {
                $q->where('patente', 'like', "%{$request->search}%")
                  ->orWhereHas('marca', fn($b) => $b->where('nombre', 'like', "%{$request->search}%"))
                  ->orWhereHas('modelo', fn($m) => $m->where('nombre', 'like', "%{$request->search}%"));
            });
        }

        $vehiculos = $consulta->orderBy('patente')->paginate($request->integer('per_page', 25));

        return response()->json($vehiculos);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'patente'                  => 'required|string|max:20|unique:vehicles,patente',
            'tipo_vehiculo_id'         => 'required|exists:vehicle_types,id',
            'marca_id'                 => 'nullable|exists:brands,id',
            'modelo_id'                => 'nullable|exists:vehicle_models,id',
            'color_id'                 => 'nullable|exists:colors,id',
            'anio'                     => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'fecha_inicio_servicio'    => 'nullable|date',
            'funcion_id'               => 'nullable|exists:vehicle_functions,id',
            'tipo_combustible_id'      => 'nullable|exists:fuel_types,id',
            'numero_motor'             => 'nullable|string|max:100',
            'numero_chasis'            => 'nullable|string|max:100',
            'origen_financiamiento_id' => 'nullable|exists:funding_origins,id',
            'zona_id'                  => 'nullable|exists:zones,id',
            'province_id'              => 'nullable|exists:provinces,id',
            'municipio_id'             => 'nullable|exists:municipalities,id',
            'prefectura_id'            => 'nullable|exists:prefectures,id',
            'unidad_id'                => 'nullable|exists:units,id',
            'es_agregado'              => 'boolean',
            'prefectura_agregado_id'   => 'nullable|exists:prefectures,id',
            'unidad_agregado_id'       => 'nullable|exists:units,id',
            'estado_vehiculo_id'       => ['required', 'exists:vehicle_statuses,id'],
            'observaciones'            => 'nullable|string',
        ]);

        $vehiculo = Vehicle::create($datos);
        $this->servicio->refresh($vehiculo->id);

        return response()->json(
            $vehiculo->load('tipoVehiculo', 'estadoVehiculo', 'marca', 'modelo', 'prefectura', 'unidad'),
            201
        );
    }

    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load([
            'tipoVehiculo', 'marca', 'modelo', 'estadoVehiculo', 'funcion',
            'color', 'tipoCombustible', 'origenFinanciamiento',
            'zona', 'provincia', 'municipio', 'prefectura', 'unidad',
            'prefecturaAgregado', 'unidadAgregado',
            'resumenOperativo',
            'registrosMantenimiento' => fn ($q) => $q->with('categoriaMantenimiento', 'taller')
                                                      ->orderByDesc('fecha_ingreso'),
        ]);

        return response()->json($vehicle);
    }

    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $datos = $request->validate([
            'patente'                  => ['sometimes', 'string', 'max:20', Rule::unique('vehicles')->ignore($vehicle->id)],
            'tipo_vehiculo_id'         => 'sometimes|exists:vehicle_types,id',
            'marca_id'                 => 'nullable|exists:brands,id',
            'modelo_id'                => 'nullable|exists:vehicle_models,id',
            'color_id'                 => 'nullable|exists:colors,id',
            'anio'                     => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'fecha_inicio_servicio'    => 'nullable|date',
            'funcion_id'               => 'nullable|exists:vehicle_functions,id',
            'tipo_combustible_id'      => 'nullable|exists:fuel_types,id',
            'numero_motor'             => 'nullable|string|max:100',
            'numero_chasis'            => 'nullable|string|max:100',
            'origen_financiamiento_id' => 'nullable|exists:funding_origins,id',
            'zona_id'                  => 'nullable|exists:zones,id',
            'province_id'              => 'nullable|exists:provinces,id',
            'municipio_id'             => 'nullable|exists:municipalities,id',
            'prefectura_id'            => 'nullable|exists:prefectures,id',
            'unidad_id'                => 'nullable|exists:units,id',
            'es_agregado'              => 'boolean',
            'prefectura_agregado_id'   => 'nullable|exists:prefectures,id',
            'unidad_agregado_id'       => 'nullable|exists:units,id',
            'estado_vehiculo_id'       => ['sometimes', 'exists:vehicle_statuses,id'],
            'observaciones'            => 'nullable|string',
        ]);

        $vehicle->update($datos);
        $this->servicio->refresh($vehicle->id);

        return response()->json(
            $vehicle->fresh(['tipoVehiculo', 'estadoVehiculo', 'marca', 'modelo', 'resumenOperativo'])
        );
    }

    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $vehicle->delete();

        return response()->json(['mensaje' => 'Vehículo eliminado correctamente.']);
    }

    public function resumenOperativo(Vehicle $vehicle): JsonResponse
    {
        $resumen = $this->servicio->refresh($vehicle->id);

        return response()->json([
            'vehiculo'           => $vehicle->only('id', 'patente', 'estado_vehiculo_id'),
            'resumen'            => $resumen,
            'ultima_actualizacion' => $resumen->ultima_actualizacion,
        ]);
    }
}
