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
                $coloresEstado = ['OPERATIVO'=>'success','PANNE'=>'danger','MANTENIMIENTO'=>'warning',
                                 'BAJA'=>'secondary','FUERA_DE_SERVICIO'=>'purple','ENAJENADO'=>'primary'];
                $codigoEstado  = $vehicle->estadoVehiculo?->codigo;
            @endphp
            <span class="badge bg-{{ $coloresEstado[$codigoEstado] ?? 'secondary' }} mb-3 fs-6">
                {{ $vehicle->estadoVehiculo?->nombre ?? '—' }}
            </span>
            <table class="table table-sm small mb-0">
                <tr><th>Patente</th><td>{{ $vehicle->patente }}</td></tr>
                <tr><th>Tipo</th><td>{{ $vehicle->tipoVehiculo?->nombre ?? '—' }}</td></tr>
                <tr><th>Marca</th><td>{{ $vehicle->marca?->nombre ?? '—' }}</td></tr>
                <tr><th>Modelo</th><td>{{ $vehicle->modelo?->nombre ?? '—' }}</td></tr>
                <tr><th>Año</th><td>{{ $vehicle->anio ?? '—' }}</td></tr>
                <tr><th>Color</th><td>{{ $vehicle->color?->nombre ?? '—' }}</td></tr>
                <tr><th>Combustible</th><td>{{ $vehicle->tipoCombustible?->nombre ?? '—' }}</td></tr>
                <tr><th>Alta Servicio</th><td>{{ $vehicle->fecha_inicio_servicio?->format('d/m/Y') ?? '—' }}</td></tr>
                <tr><th>Origen</th><td>{{ $vehicle->origenFinanciamiento?->nombre ?? '—' }}</td></tr>
                @if($vehicle->es_agregado)
                <tr class="table-info"><th>Agregado a</th><td>{{ $vehicle->prefecturaAgregado?->nombre ?? '—' }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Ubicación --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-3">
            <h6 class="fw-bold mb-3">Ubicación Asignada</h6>
            <table class="table table-sm small mb-0">
                <tr><th>Zona</th><td>{{ $vehicle->zona?->nombre ?? '—' }}</td></tr>
                <tr><th>Provincia</th><td>{{ $vehicle->provincia?->nombre ?? '—' }}</td></tr>
                <tr><th>Comuna</th><td>{{ $vehicle->municipio?->nombre ?? '—' }}</td></tr>
                <tr><th>Prefectura</th><td>{{ $vehicle->prefectura?->nombre ?? '—' }}</td></tr>
                <tr><th>Unidad</th><td>{{ $vehicle->unidad?->nombre ?? '—' }}</td></tr>
            </table>
        </div>

        {{-- KPIs operativos --}}
        @if($vehicle->resumenOperativo)
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3">📊 Indicadores Operativos</h6>
            @php $resumen = $vehicle->resumenOperativo @endphp
            <div class="mb-2">
                <small class="text-muted">Disponibilidad</small>
                <div class="progress mb-1" style="height:10px">
                    <div class="progress-bar bg-success" style="width:{{ $resumen->pct_disponibilidad * 100 }}%"></div>
                </div>
                <small class="fw-bold">{{ round($resumen->pct_disponibilidad * 100, 2) }}%</small>
            </div>
            <div class="mb-2">
                <small class="text-muted">Paralizado</small>
                <div class="progress mb-1" style="height:10px">
                    <div class="progress-bar bg-danger" style="width:{{ $resumen->pct_paralizado * 100 }}%"></div>
                </div>
                <small class="fw-bold text-danger">{{ round($resumen->pct_paralizado * 100, 2) }}%</small>
            </div>
            <table class="table table-sm small mb-0 mt-2">
                <tr><th>Días en servicio</th><td>{{ number_format($resumen->dias_servicio_total) }}</td></tr>
                <tr><th>Días en taller</th><td>{{ number_format($resumen->dias_taller_total) }}</td></tr>
                <tr><th>Días operativos</th><td>{{ number_format($resumen->dias_operativos) }}</td></tr>
                <tr><th>Ingresos a taller</th><td>{{ $resumen->ingresos_taller }}</td></tr>
                <tr><th>MTTR (días prom.)</th><td>{{ $resumen->dias_mttr }}</td></tr>
                <tr><th>Costo total mant.</th><td>${{ number_format($resumen->costo_mantenimiento_total, 0, ',', '.') }}</td></tr>
            </table>
        </div>
        @endif
    </div>

    {{-- Historial mantenimiento --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3">🔧 Historial Taller ({{ $vehicle->registrosMantenimiento->count() }})</h6>
            @forelse($vehicle->registrosMantenimiento->sortByDesc('fecha_ingreso') as $registro)
            <div class="border rounded p-2 mb-2 small">
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">{{ $registro->fecha_ingreso?->format('d/m/Y') }}</span>
                    <span class="badge
                        {{ $registro->estado == 'Cerrado' ? 'bg-success' : ($registro->estado == 'Abierto' ? 'bg-danger' : 'bg-warning') }}">
                        {{ $registro->estado }}
                    </span>
                </div>
                <div>{{ $registro->categoriaMantenimiento?->nombre ?? 'Sin categoría' }} — <em>{{ $registro->tipo_mantenimiento }}</em></div>
                @if($registro->descripcion_tecnica)
                <div class="text-muted">{{ Str::limit($registro->descripcion_tecnica, 60) }}</div>
                @endif
                <div class="d-flex justify-content-between mt-1">
                    <span>{{ $registro->dias_paralizado ?? '?' }} días</span>
                    <span>${{ number_format($registro->costo_total, 0, ',', '.') }}</span>
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
