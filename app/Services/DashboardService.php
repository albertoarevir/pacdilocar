<?php

namespace App\Services;

use App\Models\MaintenanceRecord;
use App\Models\OperationalSummary;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummary(): array
    {
        $statusCounts = Vehicle::select('vehicle_status_id', DB::raw('count(*) as total'))
            ->groupBy('vehicle_status_id')
            ->with('vehicleStatus:id,code,name')
            ->get()
            ->mapWithKeys(fn($row) => [
                ($row->vehicleStatus->code ?? 'DESCONOCIDO') => $row->total
            ])
            ->toArray();

        $totalVehicles = array_sum($statusCounts);

        $workshopStats = MaintenanceRecord::select([
            DB::raw('count(*) as total_records'),
            DB::raw('sum(case when record_status = "Cerrado" then 1 else 0 end) as closed'),
            DB::raw('sum(case when record_status = "Abierto" then 1 else 0 end) as open'),
            DB::raw('sum(case when record_status = "En Diagnóstico" then 1 else 0 end) as in_diagnosis'),
            DB::raw('sum(downtime_days) as total_downtime_days'),
            DB::raw('avg(downtime_days) as avg_days_in_workshop'),
            DB::raw('sum(total_cost) as total_repair_cost'),
        ])->first();

        $byCategory = MaintenanceRecord::select([
            'maintenance_category_id',
            DB::raw('count(*) as total'),
            DB::raw('sum(total_cost) as total_cost'),
            DB::raw('avg(downtime_days) as avg_downtime'),
        ])
            ->with('maintenanceCategory:id,name')
            ->groupBy('maintenance_category_id')
            ->get();

        $byType = MaintenanceRecord::select([
            'maintenance_type',
            DB::raw('count(*) as total'),
            DB::raw('sum(total_cost) as total_cost'),
        ])
            ->groupBy('maintenance_type')
            ->get();

        $fleetAvailability = OperationalSummary::avg('availability_pct');

        $top5Vehicles = MaintenanceRecord::select([
            'vehicle_id',
            DB::raw('count(*) as entries'),
            DB::raw('sum(downtime_days) as total_downtime'),
            DB::raw('sum(total_cost) as total_cost'),
        ])
            ->with('vehicle:id,patente')
            ->groupBy('vehicle_id')
            ->orderByDesc('entries')
            ->limit(5)
            ->get();

        return [
            'fleet' => [
                'total'            => $totalVehicles,
                'by_status'        => $statusCounts,
                'avg_availability' => round(($fleetAvailability ?? 0) * 100, 2) . '%',
            ],
            'workshop'            => $workshopStats,
            'by_category'         => $byCategory,
            'by_maintenance_type' => $byType,
            'top5_vehicles'       => $top5Vehicles,
        ];
    }

    public function getFleetStatusBreakdown(): array
    {
        $statuses = VehicleStatus::orderBy('sort_order')->get();

        $counts = Vehicle::select('vehicle_status_id', DB::raw('count(*) as total'))
            ->groupBy('vehicle_status_id')
            ->pluck('total', 'vehicle_status_id');

        $total = $counts->sum();

        return $statuses->map(fn($s) => [
            'status'      => $s->code,
            'description' => $s->description ?? $s->name,
            'count'       => $counts[$s->id] ?? 0,
            'pct'         => $total > 0 ? round(($counts[$s->id] ?? 0) / $total * 100, 2) : 0,
        ])->toArray();
    }
}
