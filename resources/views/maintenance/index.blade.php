@extends('layouts.app')
@section('title', 'Control Taller')
@section('page-title', '🔧 Control de Ingresos a Taller')

@section('content')

{{-- Filtros --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="record_status" class="form-select form-select-sm">
                    <option value="">— Estado —</option>
                    @foreach(['Abierto','Cerrado','En Diagnóstico'] as $s)
                        <option value="{{ $s }}" {{ request('record_status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="maintenance_type" class="form-select form-select-sm">
                    <option value="">— Tipo —</option>
                    @foreach(['Correctivo','Preventivo','Emergencia'] as $t)
                        <option value="{{ $t }}" {{ request('maintenance_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm"
                    value="{{ request('from_date') }}" placeholder="Desde">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm"
                    value="{{ request('to_date') }}" placeholder="Hasta">
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
        <span class="fw-semibold small">{{ $records->total() }} registros</span>
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
                @forelse($records as $r)
                <tr>
                    <td class="text-muted small">{{ $r->id }}</td>
                    <td>
                        <a href="{{ route('vehicles.show', $r->vehicle_id) }}" class="fw-bold text-decoration-none">
                            {{ $r->vehicle?->patente ?? '—' }}
                        </a>
                    </td>
                    <td>{{ $r->entry_date?->format('d/m/Y') }}</td>
                    <td>{{ $r->exit_date?->format('d/m/Y') ?? '<span class="text-muted">En curso</span>' }}</td>
                    <td>
                        @if($r->downtime_days !== null)
                            <span class="{{ $r->downtime_days > 30 ? 'text-danger fw-bold' : '' }}">
                                {{ $r->downtime_days }}
                            </span>
                        @else —
                        @endif
                    </td>
                    <td><small>{{ $r->maintenanceCategory?->name ?? '—' }}</small></td>
                    <td>
                        <span class="badge
                            {{ $r->maintenance_type == 'Correctivo' ? 'bg-danger' :
                               ($r->maintenance_type == 'Preventivo' ? 'bg-success' : 'bg-warning') }}">
                            {{ $r->maintenance_type }}
                        </span>
                    </td>
                    <td><small>{{ $r->workshop?->name ?? '—' }}</small></td>
                    <td>${{ number_format($r->total_cost, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge
                            {{ $r->record_status == 'Cerrado' ? 'bg-success' :
                               ($r->record_status == 'Abierto' ? 'bg-danger' : 'bg-warning') }}">
                            {{ $r->record_status }}
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
        {{ $records->withQueryString()->links() }}
    </div>
</div>
@endsection
