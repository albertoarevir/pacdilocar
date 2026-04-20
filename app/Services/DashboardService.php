<?php

namespace App\Services;

use App\Models\MaintenanceRecord;
use App\Models\OperationalSummary;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getResumen(): array
    {
        $conteoEstados = Vehicle::select('estado_vehiculo_id', DB::raw('count(*) as total'))
            ->groupBy('estado_vehiculo_id')
            ->with('estadoVehiculo:id,codigo,nombre')
            ->get()
            ->mapWithKeys(fn($fila) => [
                ($fila->estadoVehiculo->codigo ?? 'DESCONOCIDO') => $fila->total
            ])
            ->toArray();

        $totalVehiculos = array_sum($conteoEstados);

        $estadosTaller = MaintenanceRecord::select([
            DB::raw('count(*) as total_registros'),
            DB::raw('sum(case when estado = "Cerrado" then 1 else 0 end) as cerrados'),
            DB::raw('sum(case when estado = "Abierto" then 1 else 0 end) as abiertos'),
            DB::raw('sum(case when estado = "En Diagnóstico" then 1 else 0 end) as en_diagnostico'),
            DB::raw('sum(dias_paralizado) as total_dias_paralizado'),
            DB::raw('avg(dias_paralizado) as promedio_dias_taller'),
            DB::raw('sum(costo_total) as costo_total_reparacion'),
        ])->first();

        $porCategoria = MaintenanceRecord::select([
            'categoria_id',
            DB::raw('count(*) as total'),
            DB::raw('sum(costo_total) as costo_total'),
            DB::raw('avg(dias_paralizado) as promedio_paralizado'),
        ])
            ->with('categoriaMantenimiento:id,nombre')
            ->groupBy('categoria_id')
            ->get();

        $porTipo = MaintenanceRecord::select([
            'tipo_mantenimiento',
            DB::raw('count(*) as total'),
            DB::raw('sum(costo_total) as costo_total'),
        ])
            ->groupBy('tipo_mantenimiento')
            ->get();

        $disponibilidadFlota = OperationalSummary::avg('pct_disponibilidad');

        $top5Vehiculos = MaintenanceRecord::select([
            'vehiculo_id',
            DB::raw('count(*) as ingresos'),
            DB::raw('sum(dias_paralizado) as total_paralizado'),
            DB::raw('sum(costo_total) as costo_total'),
        ])
            ->with('vehiculo:id,patente')
            ->groupBy('vehiculo_id')
            ->orderByDesc('ingresos')
            ->limit(5)
            ->get();

        return [
            'flota' => [
                'total'                => $totalVehiculos,
                'por_estado'           => $conteoEstados,
                'disponibilidad_media' => round(($disponibilidadFlota ?? 0) * 100, 2) . '%',
            ],
            'taller'               => $estadosTaller,
            'por_categoria'        => $porCategoria,
            'por_tipo_mantenimiento' => $porTipo,
            'top5_vehiculos'       => $top5Vehiculos,
        ];
    }

    public function getDesglosePorEstado(): array
    {
        $estados = VehicleStatus::orderBy('orden')->get();

        $conteos = Vehicle::select('estado_vehiculo_id', DB::raw('count(*) as total'))
            ->groupBy('estado_vehiculo_id')
            ->pluck('total', 'estado_vehiculo_id');

        $total = $conteos->sum();

        return $estados->map(fn($e) => [
            'estado'      => $e->codigo,
            'descripcion' => $e->descripcion ?? $e->nombre,
            'cantidad'    => $conteos[$e->id] ?? 0,
            'pct'         => $total > 0 ? round(($conteos[$e->id] ?? 0) / $total * 100, 2) : 0,
        ])->toArray();
    }
}
