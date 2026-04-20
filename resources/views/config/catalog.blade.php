@extends('layouts.app')
@section('title', $cfg['label'])
@section('page-title')
    <i class="bi {{ $cfg['icon'] }} me-2"></i>{{ $cfg['label'] }}
@endsection

@section('content')

{{-- Errores y mensajes --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-2">
        <span class="fw-semibold">
            <i class="bi {{ $cfg['icon'] }} me-2 text-primary"></i>{{ $cfg['label'] }}
            <span class="badge bg-secondary ms-1">{{ $items->count() }}</span>
        </span>
        <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalForm"
                onclick="openCreate()">
            <i class="bi bi-plus-circle-fill me-1"></i>Agregar {{ $cfg['labelSing'] }}
        </button>
    </div>
    <div class="card-body p-0">
        @if($items->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No hay registros aún. Haga clic en <strong>Agregar</strong> para comenzar.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px">#</th>
                            @foreach($cfg['columns'] as $col)
                                <th>{{ $col }}</th>
                            @endforeach
                            <th style="width:100px" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td class="text-muted small">{{ $item->id }}</td>
                            @foreach(($cfg['row'])($item) as $cell)
                                <td>{!! $cell !!}</td>
                            @endforeach
                            <td class="text-center" style="white-space:nowrap">
                                {{-- Editar --}}
                                <button class="btn btn-sm btn-outline-primary py-0 px-2"
                                    title="Editar"
                                    onclick="openEdit({{ $item->id }}, {{ json_encode($item->toArray()) }})">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                {{-- Eliminar --}}
                                <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                    title="Eliminar"
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes(($cfg['row'])($item)[0] ?? $item->id) }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ─── Modal Agregar / Editar ─────────────────────────────────────────── --}}
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="frmCatalog" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" id="frmMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalFormTitle">
                        <i class="bi {{ $cfg['icon'] }} me-2 text-primary"></i>
                        <span id="modalFormAction">Agregar</span> {{ $cfg['labelSing'] }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Errores de validación --}}
                    @if($errors->any())
                        <div class="alert alert-danger py-2 small">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @foreach($fields as $field)
                        <div class="mb-3 @if($field['type'] === 'checkbox') form-check form-switch @endif">

                            @if($field['type'] === 'select')
                                <label class="form-label fw-semibold">
                                    {{ $field['label'] }}@if($field['required'])<span class="text-danger ms-1">*</span>@endif
                                </label>
                                <select name="{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                                    class="form-select @error($field['name']) is-invalid @enderror">
                                    <option value="">— Seleccione —</option>
                                    @foreach($field['options'] as $optId => $optLabel)
                                        <option value="{{ $optId }}"
                                            {{ old($field['name']) == $optId ? 'selected' : '' }}>
                                            {{ $optLabel }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif($field['type'] === 'checkbox')
                                <input class="form-check-input" type="checkbox" role="switch"
                                    name="{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                                    value="1" {{ old($field['name']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="field_{{ $field['name'] }}">
                                    {{ $field['label'] }}
                                </label>

                            @elseif($field['type'] === 'number')
                                <label class="form-label fw-semibold">
                                    {{ $field['label'] }}@if($field['required'])<span class="text-danger ms-1">*</span>@endif
                                </label>
                                <input type="number" name="{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                                    class="form-control @error($field['name']) is-invalid @enderror"
                                    value="{{ old($field['name']) }}"
                                    @isset($field['min']) min="{{ $field['min'] }}" @endisset
                                    @isset($field['max']) max="{{ $field['max'] }}" @endisset>

                            @else
                                <label class="form-label fw-semibold">
                                    {{ $field['label'] }}@if($field['required'])<span class="text-danger ms-1">*</span>@endif
                                </label>
                                <input type="text" name="{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                                    class="form-control @error($field['name']) is-invalid @enderror"
                                    value="{{ old($field['name']) }}"
                                    @isset($field['max']) maxlength="{{ $field['max'] }}" @endisset
                                    @if(!empty($field['upper'])) style="text-transform:uppercase"
                                        oninput="this.value=this.value.toUpperCase()" @endif>
                            @endif

                            @error($field['name'])
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle-fill me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ─── Modal Confirmar Eliminación ────────────────────────────────────── --}}
<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">¿Eliminar el registro</p>
                <strong id="deleteItemName" class="text-danger d-block mb-2"></strong>
                <small class="text-muted">Esta acción no se puede deshacer.</small>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <form id="frmDelete" method="POST" action="" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash-fill me-1"></i>Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const BASE_URL = "{{ url('config/'.request()->route('catalog')) }}";

function openCreate() {
    document.getElementById('modalFormAction').textContent = 'Agregar';
    document.getElementById('frmMethod').value = 'POST';
    document.getElementById('frmCatalog').action = BASE_URL;
    document.getElementById('frmCatalog').reset();
}

function openEdit(id, data) {
    document.getElementById('modalFormAction').textContent = 'Editar';
    document.getElementById('frmMethod').value = 'PUT';
    document.getElementById('frmCatalog').action = BASE_URL + '/' + id;

    // Poblar cada campo con el valor del registro
    Object.entries(data).forEach(([key, val]) => {
        const el = document.getElementById('field_' + key);
        if (!el) return;
        if (el.type === 'checkbox') {
            el.checked = val == 1 || val === true;
        } else {
            el.value = val ?? '';
        }
    });

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalForm'));
    modal.show();
}

function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = '«' + name + '»';
    document.getElementById('frmDelete').action = BASE_URL + '/' + id;
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDelete'));
    modal.show();
}

// Abrir modal automáticamente si hay errores de validación (POST fallido)
@if($errors->any())
    window.addEventListener('DOMContentLoaded', () => {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalForm'));
        modal.show();
        @if(old('_method') === 'PUT')
            document.getElementById('modalFormAction').textContent = 'Editar';
        @endif
    });
@endif
</script>
@endpush
