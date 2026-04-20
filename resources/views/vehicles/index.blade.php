@extends('layouts.app')
@section('title', 'Vehículos')
@section('page-title', '🚗 Registro de Flota Vehicular')

@section('content')

{{-- Filtros + botón Nuevo --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Patente, marca, modelo..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">— Estado —</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->codigo }}" {{ request('status') == $estado->codigo ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <button class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Limpiar</a>
            </div>
            <div class="col-md-auto ms-auto">
                <a href="{{ route('vehicles.create') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-circle-fill me-1"></i> Nuevo Vehículo
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold small">{{ $vehiculos->total() }} vehículos encontrados</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Patente</th>
                    <th>Tipo</th>
                    <th>Marca / Modelo</th>
                    <th>Año</th>
                    <th>Zona</th>
                    <th>Prefectura</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehiculos as $vehiculo)
                <tr>
                    <td class="fw-bold">{{ $vehiculo->patente }}</td>
                    <td><small>{{ $vehiculo->tipoVehiculo?->codigo }}</small></td>
                    <td>{{ $vehiculo->marca?->nombre }} {{ $vehiculo->modelo?->nombre }}</td>
                    <td>{{ $vehiculo->anio }}</td>
                    <td><small>{{ $vehiculo->zona?->nombre ?? '—' }}</small></td>
                    <td><small>{{ $vehiculo->prefectura?->nombre ?? '—' }}</small></td>
                    <td>
                        @php
                            $coloresEstado = [
                                'OPERATIVO'         => 'success',
                                'PANNE'             => 'danger',
                                'MANTENIMIENTO'     => 'warning',
                                'BAJA'              => 'secondary',
                                'FUERA_DE_SERVICIO' => 'purple',
                                'ENAJENADO'         => 'primary',
                            ];
                            $colorEstado = $coloresEstado[$vehiculo->estadoVehiculo?->codigo] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $colorEstado }}">{{ $vehiculo->estadoVehiculo?->nombre ?? '—' }}</span>
                        @if($vehiculo->es_agregado)
                            <span class="badge bg-info ms-1" title="Vehículo agregado">AGR</span>
                        @endif
                    </td>
                    <td class="text-center" style="white-space:nowrap">
                        {{-- Ver detalle --}}
                        <a href="{{ route('vehicles.show', $vehiculo) }}"
                           class="btn btn-sm btn-outline-primary py-0 px-2"
                           title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </a>
                        {{-- Editar --}}
                        <a href="{{ route('vehicles.edit', $vehiculo) }}"
                           class="btn btn-sm btn-outline-warning py-0 px-2 ms-1"
                           title="Editar vehículo">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        {{-- Eliminar --}}
                        <button type="button"
                                class="btn btn-sm btn-outline-danger py-0 px-2 ms-1 btn-eliminar"
                                title="Eliminar vehículo"
                                data-id="{{ $vehiculo->id }}"
                                data-patente="{{ $vehiculo->patente }}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>Sin resultados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $vehiculos->withQueryString()->links() }}
    </div>
</div>

{{-- Modal Confirmar Eliminación --}}
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-trash3-fill text-danger" style="font-size:3rem"></i>
                <p class="mt-3 mb-1 fs-6">¿Está seguro que desea eliminar el vehículo?</p>
                <p class="fw-bold fs-5 text-danger mb-0" id="modalPatente"></p>
                <p class="text-muted small mt-2">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <form id="formEliminar" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-trash3-fill me-1"></i>Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('modalPatente').textContent = btn.dataset.patente;
        document.getElementById('formEliminar').action = `/vehicles/${btn.dataset.id}`;
    });
});
</script>
@endpush
