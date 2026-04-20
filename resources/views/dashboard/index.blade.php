@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', '📋 Dashboard — Resumen Ejecutivo')

@section('content')

{{-- Tarjetas KPI --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card bg-primary text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small opacity-75">Total Flota</div>
                    <div class="fs-2 fw-bold">{{ $stats['fleet']['total'] }}</div>
                </div>
                <i class="bi bi-collection icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card bg-success text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small opacity-75">Operativos</div>
                    <div class="fs-2 fw-bold">{{ $stats['fleet']['by_status']['OPERATIVO'] ?? 0 }}</div>
                </div>
                <i class="bi bi-check-circle icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card bg-danger text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small opacity-75">En Panne</div>
                    <div class="fs-2 fw-bold">{{ $stats['fleet']['by_status']['PANNE'] ?? 0 }}</div>
                </div>
                <i class="bi bi-exclamation-triangle icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card bg-warning text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small opacity-75">Mantenimiento</div>
                    <div class="fs-2 fw-bold">{{ $stats['fleet']['by_status']['MANTENIMIENTO'] ?? 0 }}</div>
                </div>
                <i class="bi bi-tools icon"></i>
            </div>
        </div>
    </div>
</div>

{{-- Segunda fila --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="small text-muted">Fuera de Servicio</div>
            <div class="fs-3 fw-bold text-purple">{{ $stats['fleet']['by_status']['FUERA_DE_SERVICIO'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="small text-muted">Enajenados</div>
            <div class="fs-3 fw-bold text-secondary">{{ $stats['fleet']['by_status']['ENAJENADO'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="small text-muted">Total Días Downtime</div>
            <div class="fs-3 fw-bold text-danger">{{ number_format($stats['workshop']->total_downtime_days ?? 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="small text-muted">Gasto Mantenimiento</div>
            <div class="fs-4 fw-bold text-dark">${{ number_format($stats['workshop']->total_repair_cost ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Gráfico estados --}}
    <div class="col-md-5">
        <div class="card stat-card p-3">
            <h6 class="fw-semibold mb-3">Estado de la Flota</h6>
            <canvas id="statusChart" height="220"></canvas>
        </div>
    </div>

    {{-- Gráfico categorías falla --}}
    <div class="col-md-7">
        <div class="card stat-card p-3">
            <h6 class="fw-semibold mb-3">Costos por Categoría de Falla</h6>
            <canvas id="categoryChart" height="220"></canvas>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Estado registros taller --}}
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <h6 class="fw-semibold mb-3">Registros Taller</h6>
            <table class="table table-sm mb-0">
                <tbody>
                    <tr><td>Total registros</td><td class="fw-bold text-end">{{ $stats['workshop']->total_records ?? 0 }}</td></tr>
                    <tr><td>✅ Cerrados</td><td class="fw-bold text-success text-end">{{ $stats['workshop']->closed ?? 0 }}</td></tr>
                    <tr><td>🔴 Abiertos</td><td class="fw-bold text-danger text-end">{{ $stats['workshop']->open ?? 0 }}</td></tr>
                    <tr><td>🔍 En Diagnóstico</td><td class="fw-bold text-warning text-end">{{ $stats['workshop']->in_diagnosis ?? 0 }}</td></tr>
                    <tr class="table-light"><td>Días prom. en taller</td><td class="fw-bold text-end">{{ round($stats['workshop']->avg_days_in_workshop ?? 0, 1) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tipo de mantencion --}}
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <h6 class="fw-semibold mb-3">Tipo de Mantención</h6>
            @foreach($stats['by_maintenance_type'] as $tipo)
            <div class="mb-2">
                <div class="d-flex justify-content-between small">
                    <span>{{ $tipo->maintenance_type }}</span>
                    <span class="fw-bold">{{ $tipo->total }}</span>
                </div>
                <div class="progress" style="height:6px">
                    @php $pct = $stats['workshop']->total_records > 0 ? ($tipo->total / $stats['workshop']->total_records * 100) : 0 @endphp
                    <div class="progress-bar
                        {{ $tipo->maintenance_type == 'Correctivo' ? 'bg-danger' : ($tipo->maintenance_type == 'Preventivo' ? 'bg-success' : 'bg-warning') }}"
                        style="width:{{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top 5 vehículos --}}
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <h6 class="fw-semibold mb-3">🏆 Top 5 — Más Ingresos Taller</h6>
            @foreach($stats['top5_vehicles'] as $i => $v)
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <span class="badge bg-secondary me-1">#{{ $i+1 }}</span>
                    <span class="fw-semibold">{{ $v->vehicle->patente ?? '—' }}</span>
                </div>
                <div class="text-end small">
                    <span class="text-danger">{{ $v->entries }} ing.</span>
                    <span class="text-muted ms-2">{{ $v->total_downtime }} días</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const statusLabels = @json(array_keys($stats['fleet']['by_status']));
const statusData   = @json(array_values($stats['fleet']['by_status']));

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusData,
            backgroundColor: ['#198754','#dc3545','#fd7e14','#6c757d','#6f42c1','#0d6efd'],
        }]
    },
    options: { plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});

const catData = @json($stats['by_category']);
new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: catData.map(c => c.maintenance_category?.name ?? 'S/C'),
        datasets: [{
            label: 'Costo Total ($)',
            data: catData.map(c => c.total_cost),
            backgroundColor: '#1a2744cc',
            borderRadius: 4,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => '$' + v.toLocaleString('es-CL') } }
        }
    }
});
</script>
@endpush
