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
 *  - Downtime (%)       = días_en_taller / días_en_servicio
 *  - MTTR (días)        = días_en_taller / N° ingresos a taller
 */
class OperationalSummaryService
{
    public function refresh(int $vehicleId): OperationalSummary
    {
        $vehicle = Vehicle::with('maintenanceRecords')->findOrFail($vehicleId);

        $totalServiceDays  = $this->computeServiceDays($vehicle);
        $totalWorkshopDays = $this->computeWorkshopDays($vehicle);
        $workshopEntries   = $vehicle->maintenanceRecords()->withTrashed(false)->count();
        $totalCost         = $vehicle->maintenanceRecords()->withTrashed(false)->sum('total_cost');

        $operationalDays = max(0, $totalServiceDays - $totalWorkshopDays);

        $availabilityPct = $totalServiceDays > 0
            ? round($operationalDays / $totalServiceDays, 6)
            : 0.0;

        $downtimePct = $totalServiceDays > 0
            ? round($totalWorkshopDays / $totalServiceDays, 6)
            : 0.0;

        $mttrDays = $workshopEntries > 0
            ? round($totalWorkshopDays / $workshopEntries, 2)
            : 0.0;

        return OperationalSummary::updateOrCreate(
            ['vehicle_id' => $vehicleId],
            [
                'total_service_days'     => $totalServiceDays,
                'total_workshop_days'    => $totalWorkshopDays,
                'operational_days'       => $operationalDays,
                'availability_pct'       => $availabilityPct,
                'downtime_pct'           => $downtimePct,
                'workshop_entries'       => $workshopEntries,
                'total_maintenance_cost' => $totalCost,
                'mttr_days'              => $mttrDays,
                'last_computed_at'       => now(),
            ]
        );
    }

    public function refreshAll(): void
    {
        Vehicle::whereNotNull('service_start_date')->each(
            fn (Vehicle $v) => $this->refresh($v->id)
        );
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function computeServiceDays(Vehicle $vehicle): int
    {
        if (! $vehicle->service_start_date) {
            return 0;
        }

        $endDate = in_array($vehicle->vehicleStatus?->code, ['BAJA', 'ENAJENADO'])
            ? Carbon::parse($vehicle->updated_at)
            : Carbon::now();

        return (int) Carbon::parse($vehicle->service_start_date)->diffInDays($endDate);
    }

    private function computeWorkshopDays(Vehicle $vehicle): int
    {
        return (int) $vehicle->maintenanceRecords()
            ->whereNotNull('downtime_days')
            ->sum('downtime_days');
    }
}
