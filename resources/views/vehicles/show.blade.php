@extends('layouts.app')
@section('title', 'Vehículo ' . $vehicle->patente)
@section('page-title', '🚗 Vehículo: ' . $vehicle->patente)

@section('content')
<div class="row g-3">

    {{-- Datos del vehículo --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3">Identificación</h6>
            @php
                $statusColors = ['OPERATIVO'=>'success','PANNE'=>'danger','MANTENIMIENTO'=>'warning',
                                 'BAJA'=>'secondary','FUERA_DE_SERVICIO'=>'purple','ENAJENADO'=>'primary'];
                $statusCode   = $vehicle->vehicleStatus?->code;
            @endphp
            <span class="badge bg-{{ $statusColors[$statusCode] ?? 'secondary' }} mb-3 fs-6">
                {{ $vehicle->vehicleStatus?->name ?? '—' }}
            </span>
            <table class="table table-sm small mb-0">
                <tr><th>Patente</th><td>{{ $vehicle->patente }}</td></tr>
                <tr><th>Tipo</th><td>{{ $vehicle->vehicleType?->name ?? '—' }}</td></tr>
                <tr><th>Marca</th><td>{{ $vehicle->brand?->name ?? '—' }}</td></tr>
                <tr><th>Modelo</th><td>{{ $vehicle->vehicleModel?->name ?? '—' }}</td></tr>
                <tr><th>Año</th><td>{{ $vehicle->year ?? '—' }}</td></tr>
                <tr><th>Color</th><td>{{ $vehicle->color?->name ?? '—' }}</td></tr>
                <tr><th>Combustible</th><td>{{ $vehicle->fuelType?->name ?? '—' }}</td></tr>
                <tr><th>Alta Servicio</th><td>{{ $vehicle->service_start_date?->format('d/m/Y') ?? '—' }}</td></tr>
                <tr><th>Origen</th><td>{{ $vehicle->fundingOrigin?->name ?? '—' }}</td></tr>
                @if($vehicle->is_aggregated)
                <tr class="table-info"><th>Agregado a</th><td>{{ $vehicle->aggregatePrefecture?->name ?? '—' }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Ubicación --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-3">
            <h6 class="fw-bold mb-3">Ubicación Asignada</h6>
            <table class="table table-sm small mb-0">
                <tr><th>Zona</th><td>{{ $vehicle->zone?->name ?? '—' }}</td></tr>
                <tr><th>Provincia</th><td>{{ $vehicle->province?->name ?? '—' }}</td></tr>
                <tr><th>Comuna</th><td>{{ $vehicle->municipality?->name ?? '—' }}</td></tr>
                <tr><th>Prefectura</th><td>{{ $vehicle->prefecture?->name ?? '—' }}</td></tr>
                <tr><th>Unidad</th><td>{{ $vehicle->unit?->name ?? '—' }}</td></tr>
            </table>
        </div>

        {{-- KPIs operativos --}}
        @if($vehicle->operationalSummary)
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3">📊 Indicadores Operativos</h6>
            @php $s = $vehicle->operationalSummary @endphp
            <div class="mb-2">
                <small class="text-muted">Disponibilidad</small>
                <div class="progress mb-1" style="height:10px">
                    <div class="progress-bar bg-success" style="width:{{ $s->availability_pct * 100 }}%"></div>
                </div>
                <small class="fw-bold">{{ round($s->availability_pct * 100, 2) }}%</small>
            </div>
            <div class="mb-2">
                <small class="text-muted">Downtime</small>
                <div class="progress mb-1" style="height:10px">
                    <div class="progress-bar bg-danger" style="width:{{ $s->downtime_pct * 100 }}%"></div>
                </div>
                <small class="fw-bold text-danger">{{ round($s->downtime_pct * 100, 2) }}%</small>
            </div>
            <table class="table table-sm small mb-0 mt-2">
                <tr><th>Días en servicio</th><td>{{ number_format($s->total_service_days) }}</td></tr>
                <tr><th>Días en taller</th><td>{{ number_format($s->total_workshop_days) }}</td></tr>
                <tr><th>Días operativos</th><td>{{ number_format($s->operational_days) }}</td></tr>
                <tr><th>Ingresos a taller</th><td>{{ $s->workshop_entries }}</td></tr>
                <tr><th>MTTR (días prom.)</th><td>{{ $s->mttr_days }}</td></tr>
                <tr><th>Costo total mant.</th><td>${{ number_format($s->total_maintenance_cost, 0, ',', '.') }}</td></tr>
            </table>
        </div>
        @endif
    </div>

    {{-- Historial mantenimiento --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3">🔧 Historial Taller ({{ $vehicle->maintenanceRecords->count() }})</h6>
            @forelse($vehicle->maintenanceRecords->sortByDesc('entry_date') as $m)
            <div class="border rounded p-2 mb-2 small">
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">{{ $m->entry_date?->format('d/m/Y') }}</span>
                    <span class="badge
                        {{ $m->record_status == 'Cerrado' ? 'bg-success' : ($m->record_status == 'Abierto' ? 'bg-danger' : 'bg-warning') }}">
                        {{ $m->record_status }}
                    </span>
                </div>
                <div>{{ $m->maintenanceCategory?->name ?? 'Sin categoría' }} — <em>{{ $m->maintenance_type }}</em></div>
                @if($m->technical_description)
                <div class="text-muted">{{ Str::limit($m->technical_description, 60) }}</div>
                @endif
                <div class="d-flex justify-content-between mt-1">
                    <span>{{ $m->downtime_days ?? '?' }} días</span>
                    <span>${{ number_format($m->total_cost, 0, ',', '.') }}</span>
                </div>
            </div>
            @empty
            <p class="text-muted small">Sin registros de taller.</p>
            @endforelse
        </div>
    </div>

</div>

<div class="mt-3">
    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>
@endsection
