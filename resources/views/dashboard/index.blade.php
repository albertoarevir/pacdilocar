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
                    <div class="fs-2 fw-bold">{{ $estadisticas['flota']['total'] }}</div>
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
                    <div class="fs-2 fw-bold">{{ $estadisticas['flota']['por_estado']['OPERATIVO'] ?? 0 }}</div>
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
                    <div class="fs-2 fw-bold">{{ $estadisticas['flota']['por_estado']['PANNE'] ?? 0 }}</div>
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
                    <div class="fs-2 fw-bold">{{ $estadisticas['flota']['por_estado']['MANTENIMIENTO'] ?? 0 }}</div>
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
            <div class="fs-3 fw-bold text-purple">{{ $estadisticas['flota']['por_estado']['FUERA_DE_SERVICIO'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="small text-muted">Enajenados</div>
            <div class="fs-3 fw-bold text-secondary">{{ $estadisticas['flota']['por_estado']['ENAJENADO'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="small text-muted">Total Días Paralizado</div>
            <div class="fs-3 fw-bold text-danger">{{ number_format($estadisticas['taller']->total_dias_paralizado ?? 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="small text-muted">Gasto Mantenimiento</div>
            <div class="fs-4 fw-bold text-dark">${{ number_format($estadisticas['taller']->costo_total_reparacion ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Gráfico estados --}}
    <div class="col-md-5">
        <div class="card stat-card p-3">
            <h6 class="fw-semibold mb-3">Estado de la Flota</h6>
            <canvas id="graficoEstados" height="220"></canvas>
        </div>
    </div>

    {{-- Gráfico categorías falla --}}
    <div class="col-md-7">
        <div class="card stat-card p-3">
            <h6 class="fw-semibold mb-3">Costos por Categoría de Falla</h6>
            <canvas id="graficoCategorias" height="220"></canvas>
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
                    <tr><td>Total registros</td><td class="fw-bold text-end">{{ $estadisticas['taller']->total_registros ?? 0 }}</td></tr>
                    <tr><td>✅ Cerrados</td><td class="fw-bold text-success text-end">{{ $estadisticas['taller']->cerrados ?? 0 }}</td></tr>
                    <tr><td>🔴 Abiertos</td><td class="fw-bold text-danger text-end">{{ $estadisticas['taller']->abiertos ?? 0 }}</td></tr>
                    <tr><td>🔍 En Diagnóstico</td><td class="fw-bold text-warning text-end">{{ $estadisticas['taller']->en_diagnostico ?? 0 }}</td></tr>
                    <tr class="table-light"><td>Días prom. en taller</td><td class="fw-bold text-end">{{ round($estadisticas['taller']->promedio_dias_taller ?? 0, 1) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tipo de mantención --}}
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <h6 class="fw-semibold mb-3">Tipo de Mantención</h6>
            @foreach($estadisticas['por_tipo_mantenimiento'] as $tipo)
            <div class="mb-2">
                <div class="d-flex justify-content-between small">
                    <span>{{ $tipo->tipo_mantenimiento }}</span>
                    <span class="fw-bold">{{ $tipo->total }}</span>
                </div>
                <div class="progress" style="height:6px">
                    @php $pct = ($estadisticas['taller']->total_registros ?? 0) > 0 ? ($tipo->total / $estadisticas['taller']->total_registros * 100) : 0 @endphp
                    <div class="progress-bar
                        {{ $tipo->tipo_mantenimiento == 'Correctivo' ? 'bg-danger' : ($tipo->tipo_mantenimiento == 'Preventivo' ? 'bg-success' : 'bg-warning') }}"
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
            @foreach($estadisticas['top5_vehiculos'] as $i => $item)
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <span class="badge bg-secondary me-1">#{{ $i+1 }}</span>
                    <span class="fw-semibold">{{ $item->vehiculo->patente ?? '—' }}</span>
                </div>
                <div class="text-end small">
                    <span class="text-danger">{{ $item->ingresos }} ing.</span>
                    <span class="text-muted ms-2">{{ $item->total_paralizado }} días</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const etiquetasEstado = @json(array_keys($estadisticas['flota']['por_estado']));
const datosEstado     = @json(array_values($estadisticas['flota']['por_estado']));

new Chart(document.getElementById('graficoEstados'), {
    type: 'doughnut',
    data: {
        labels: etiquetasEstado,
        datasets: [{
            data: datosEstado,
            backgroundColor: ['#198754','#dc3545','#fd7e14','#6c757d','#6f42c1','#0d6efd'],
        }]
    },
    options: { plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});

const datosCategorias = @json($estadisticas['por_categoria']);
new Chart(document.getElementById('graficoCategorias'), {
    type: 'bar',
    data: {
        labels: datosCategorias.map(c => c.categoria_mantenimiento?.nombre ?? 'S/C'),
        datasets: [{
            label: 'Costo Total ($)',
            data: datosCategorias.map(c => c.costo_total),
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
