<?php

namespace App\Services;

use App\Models\OperationalSummary;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;

/**
 * Recalcula y persiste los indicadores operativos de un vehículo.
 *
 * KPIs:
 *  - Disponibilidad (%) = días_operativos / días_en_servicio
 *  - Paralizado (%)     = días_en_taller / días_en_servicio
 *  - MTTR (días)        = días_en_taller / N° ingresos a taller
 */
class OperationalSummaryService
{
    public function refresh(int $vehiculoId): OperationalSummary
    {
        $vehiculo = Vehicle::with('registrosMantenimiento')->findOrFail($vehiculoId);

        $diasServicioTotal  = $this->calcularDiasServicio($vehiculo);
        $diasTallerTotal    = $this->calcularDiasTaller($vehiculo);
        $ingresosTaller     = $vehiculo->registrosMantenimiento()->withTrashed(false)->count();
        $costoTotal         = $vehiculo->registrosMantenimiento()->withTrashed(false)->sum('costo_total');

        $diasOperativos = max(0, $diasServicioTotal - $diasTallerTotal);

        $pctDisponibilidad = $diasServicioTotal > 0
            ? round($diasOperativos / $diasServicioTotal, 6)
            : 0.0;

        $pctParalizado = $diasServicioTotal > 0
            ? round($diasTallerTotal / $diasServicioTotal, 6)
            : 0.0;

        $diasMttr = $ingresosTaller > 0
            ? round($diasTallerTotal / $ingresosTaller, 2)
            : 0.0;

        return OperationalSummary::updateOrCreate(
            ['vehiculo_id' => $vehiculoId],
            [
                'dias_servicio_total'       => $diasServicioTotal,
                'dias_taller_total'         => $diasTallerTotal,
                'dias_operativos'           => $diasOperativos,
                'pct_disponibilidad'        => $pctDisponibilidad,
                'pct_paralizado'            => $pctParalizado,
                'ingresos_taller'           => $ingresosTaller,
                'costo_mantenimiento_total' => $costoTotal,
                'dias_mttr'                 => $diasMttr,
                'ultima_actualizacion'      => now(),
            ]
        );
    }

    public function refreshAll(): void
    {
        Vehicle::whereNotNull('fecha_inicio_servicio')->each(
            fn (Vehicle $v) => $this->refresh($v->id)
        );
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function calcularDiasServicio(Vehicle $vehiculo): int
    {
        if (! $vehiculo->fecha_inicio_servicio) {
            return 0;
        }

        $fechaFin = in_array($vehiculo->estadoVehiculo?->codigo, ['BAJA', 'ENAJENADO'])
            ? Carbon::parse($vehiculo->updated_at)
            : Carbon::now();

        return (int) Carbon::parse($vehiculo->fecha_inicio_servicio)->diffInDays($fechaFin);
    }

    private function calcularDiasTaller(Vehicle $vehiculo): int
    {
        return (int) $vehiculo->registrosMantenimiento()
            ->whereNotNull('dias_paralizado')
            ->sum('dias_paralizado');
    }
}
