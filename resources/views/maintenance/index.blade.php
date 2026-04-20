@extends('layouts.app')
@section('title', 'Control Taller')
@section('page-title', '🔧 Control de Ingresos a Taller')

@section('content')

{{-- Filtros --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="estado" class="form-select form-select-sm">
                    <option value="">— Estado —</option>
                    @foreach(['Abierto','Cerrado','En Diagnóstico'] as $est)
                        <option value="{{ $est }}" {{ request('estado') == $est ? 'selected' : '' }}>{{ $est }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipo_mantenimiento" class="form-select form-select-sm">
                    <option value="">— Tipo —</option>
                    @foreach(['Correctivo','Preventivo','Emergencia'] as $tipo)
                        <option value="{{ $tipo }}" {{ request('tipo_mantenimiento') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="desde" class="form-control form-control-sm"
                    value="{{ request('desde') }}" placeholder="Desde">
            </div>
            <div class="col-md-2">
                <input type="date" name="hasta" class="form-control form-control-sm"
                    value="{{ request('hasta') }}" placeholder="Hasta">
            </div>
            <div class="col-md-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Limpiar</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-2">
        <span class="fw-semibold small">{{ $registros->total() }} registros</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>N°</th>
                    <th>Vehículo</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Días</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Taller</th>
                    <th>Costo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                <tr>
                    <td class="text-muted small">{{ $registro->id }}</td>
                    <td>
                        <a href="{{ route('vehicles.show', $registro->vehiculo_id) }}" class="fw-bold text-decoration-none">
                            {{ $registro->vehiculo?->patente ?? '—' }}
                        </a>
                    </td>
                    <td>{{ $registro->fecha_ingreso?->format('d/m/Y') }}</td>
                    <td>{{ $registro->fecha_salida?->format('d/m/Y') ?? '<span class="text-muted">En curso</span>' }}</td>
                    <td>
                        @if($registro->dias_paralizado !== null)
                            <span class="{{ $registro->dias_paralizado > 30 ? 'text-danger fw-bold' : '' }}">
                                {{ $registro->dias_paralizado }}
                            </span>
                        @else —
                        @endif
                    </td>
                    <td><small>{{ $registro->categoriaMantenimiento?->nombre ?? '—' }}</small></td>
                    <td>
                        <span class="badge
                            {{ $registro->tipo_mantenimiento == 'Correctivo' ? 'bg-danger' :
                               ($registro->tipo_mantenimiento == 'Preventivo' ? 'bg-success' : 'bg-warning') }}">
                            {{ $registro->tipo_mantenimiento }}
                        </span>
                    </td>
                    <td><small>{{ $registro->taller?->nombre ?? '—' }}</small></td>
                    <td>${{ number_format($registro->costo_total, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge
                            {{ $registro->estado == 'Cerrado' ? 'bg-success' :
                               ($registro->estado == 'Abierto' ? 'bg-danger' : 'bg-warning') }}">
                            {{ $registro->estado }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">Sin registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $registros->withQueryString()->links() }}
    </div>
</div>
@endsection
