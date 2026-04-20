@extends('layouts.app')
@section('title', 'Nuevo Vehículo')
@section('page-title', '🚗 Ingresar Nuevo Vehículo')

@section('content')

<form method="POST" action="{{ route('vehicles.store') }}" novalidate id="formVehiculo">
@csrf

<div class="row g-3">

    {{-- ── COLUMNA IZQUIERDA ─────────────────────────────────────────── --}}
    <div class="col-lg-6">

        {{-- Identificación --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold py-2">
                <i class="bi bi-card-text me-2 text-primary"></i>Identificación del Vehículo
            </div>
            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Patente / Sigla <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="text" name="patente" id="inputPatente"
                                class="form-control text-uppercase @error('patente') is-invalid @enderror"
                                value="{{ old('patente') }}"
                                placeholder="Ej: RP-1234"
                                maxlength="20"
                                autocomplete="off"
                                style="text-transform:uppercase">
                            <span class="input-group-text bg-white px-2" id="patenteStatusIcon">
                                <i class="bi bi-dash text-secondary" id="patenteIcon"></i>
                            </span>
                        </div>
                        @error('patente')
                            <div class="text-danger small mt-1">
                                <i class="bi bi-x-circle-fill me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <div id="patenteMsg" class="small mt-1 d-none"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Tipo de Vehículo <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_vehiculo_id"
                            class="form-select @error('tipo_vehiculo_id') is-invalid @enderror">
                            <option value="">— Seleccione —</option>
                            @foreach($tiposVehiculo as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_vehiculo_id') == $tipo->id ? 'selected' : '' }}>
                                    [{{ $tipo->codigo }}] {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_vehiculo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Marca</label>
                        <select name="marca_id" id="selectMarca"
                            class="form-select @error('marca_id') is-invalid @enderror">
                            <option value="">— Seleccione marca —</option>
                            @foreach($marcasConIds as $m)
                                <option value="{{ $m['id'] }}"
                                    {{ old('marca_id') == $m['id'] ? 'selected' : '' }}>
                                    {{ $m['nombre'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('marca_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Modelo</label>
                        <select name="modelo_id" id="selectModelo"
                            class="form-select @error('modelo_id') is-invalid @enderror"
                            {{ old('marca_id') ? '' : 'disabled' }}>
                            <option value="">— Seleccione primero la marca —</option>
                        </select>
                        @error('modelo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="modeloHint">
                            Selecciona una marca para ver los modelos disponibles.
                        </small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Año</label>
                        <input type="number" name="anio"
                            class="form-control @error('anio') is-invalid @enderror"
                            value="{{ old('anio') }}"
                            min="1950" max="{{ date('Y') + 1 }}"
                            placeholder="{{ date('Y') }}">
                        @error('anio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Color</label>
                        <select name="color_id"
                            class="form-select @error('color_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($colores as $color)
                                <option value="{{ $color->id }}" {{ old('color_id') == $color->id ? 'selected' : '' }}>
                                    {{ $color->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('color_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Combustible</label>
                        <select name="tipo_combustible_id"
                            class="form-select @error('tipo_combustible_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($tiposCombustible as $tc)
                                <option value="{{ $tc->id }}" {{ old('tipo_combustible_id') == $tc->id ? 'selected' : '' }}>
                                    {{ $tc->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_combustible_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">N° Motor</label>
                        <input type="text" name="numero_motor"
                            class="form-control @error('numero_motor') is-invalid @enderror"
                            value="{{ old('numero_motor') }}" maxlength="100">
                        @error('numero_motor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">N° Chasis</label>
                        <input type="text" name="numero_chasis"
                            class="form-control @error('numero_chasis') is-invalid @enderror"
                            value="{{ old('numero_chasis') }}" maxlength="100">
                        @error('numero_chasis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha Alta en Servicio</label>
                        <input type="date" name="fecha_inicio_servicio"
                            class="form-control @error('fecha_inicio_servicio') is-invalid @enderror"
                            value="{{ old('fecha_inicio_servicio') }}">
                        @error('fecha_inicio_servicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Estado <span class="text-danger">*</span>
                        </label>
                        <select name="estado_vehiculo_id"
                            class="form-select @error('estado_vehiculo_id') is-invalid @enderror">
                            <option value="">— Seleccione —</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}"
                                    {{ old('estado_vehiculo_id', $estados->firstWhere('codigo','OPERATIVO')?->id) == $estado->id ? 'selected' : '' }}>
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado_vehiculo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Función que Desarrolla</label>
                        <select name="funcion_id"
                            class="form-select @error('funcion_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($funciones as $fn)
                                <option value="{{ $fn->id }}" {{ old('funcion_id') == $fn->id ? 'selected' : '' }}>
                                    {{ $fn->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('funcion_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Origen Financiamiento</label>
                        <select name="origen_financiamiento_id"
                            class="form-select @error('origen_financiamiento_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($origenesFinanciamiento as $of)
                                <option value="{{ $of->id }}" {{ old('origen_financiamiento_id') == $of->id ? 'selected' : '' }}>
                                    {{ $of->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('origen_financiamiento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- ── COLUMNA DERECHA ──────────────────────────────────────────── --}}
    <div class="col-lg-6">

        {{-- Ubicación --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold py-2">
                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>Ubicación y Asignación
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label fw-semibold">Jefatura de Zona</label>
                        <select name="zona_id" id="selectZona"
                            class="form-select @error('zona_id') is-invalid @enderror">
                            <option value="">— Seleccione Zona —</option>
                            @foreach($zonas as $zona)
                                <option value="{{ $zona->id }}" {{ old('zona_id') == $zona->id ? 'selected' : '' }}>
                                    {{ $zona->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('zona_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Provincia</label>
                        <select name="province_id" id="selectProvincia"
                            class="form-select @error('province_id') is-invalid @enderror"
                            {{ old('zona_id') ? '' : 'disabled' }}>
                            <option value="">— Seleccione primero la Zona —</option>
                        </select>
                        @error('province_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Comuna</label>
                        <select name="municipio_id" id="selectMunicipio"
                            class="form-select @error('municipio_id') is-invalid @enderror"
                            {{ old('province_id') ? '' : 'disabled' }}>
                            <option value="">— Seleccione primero la Provincia —</option>
                        </select>
                        @error('municipio_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prefectura Asignada</label>
                        <select name="prefectura_id"
                            class="form-select @error('prefectura_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($prefecturas as $pf)
                                <option value="{{ $pf->id }}" {{ old('prefectura_id') == $pf->id ? 'selected' : '' }}>
                                    {{ $pf->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('prefectura_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad Asignada</label>
                        <select name="unidad_id"
                            class="form-select @error('unidad_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}" {{ old('unidad_id') == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('unidad_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- Agregación --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold py-2">
                <i class="bi bi-arrow-left-right me-2 text-warning"></i>Vehículo Agregado
            </div>
            <div class="card-body">

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch"
                        name="es_agregado" id="es_agregado" value="1"
                        {{ old('es_agregado') ? 'checked' : '' }}
                        onchange="toggleAgregado(this.checked)">
                    <label class="form-check-label fw-semibold" for="es_agregado">
                        Este vehículo está agregado a otra unidad
                    </label>
                </div>

                <div id="seccionAgregado" style="{{ old('es_agregado') ? '' : 'display:none' }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Prefectura Agregado</label>
                            <select name="prefectura_agregado_id"
                                class="form-select @error('prefectura_agregado_id') is-invalid @enderror">
                                <option value="">— Sin especificar —</option>
                                @foreach($prefecturas as $pf)
                                    <option value="{{ $pf->id }}"
                                        {{ old('prefectura_agregado_id') == $pf->id ? 'selected' : '' }}>
                                        {{ $pf->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('prefectura_agregado_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unidad/Dest. Agregado</label>
                            <select name="unidad_agregado_id"
                                class="form-select @error('unidad_agregado_id') is-invalid @enderror">
                                <option value="">— Sin especificar —</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}"
                                        {{ old('unidad_agregado_id') == $unidad->id ? 'selected' : '' }}>
                                        {{ $unidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unidad_agregado_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Observaciones --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold py-2">
                <i class="bi bi-chat-text me-2 text-secondary"></i>Observaciones
            </div>
            <div class="card-body">
                <textarea name="observaciones" rows="3"
                    class="form-control @error('observaciones') is-invalid @enderror"
                    placeholder="Observaciones adicionales del vehículo..."
                    maxlength="1000">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Máximo 1000 caracteres.</small>
            </div>
        </div>

    </div>
</div>

{{-- Botones de acción --}}
<div class="d-flex gap-2 justify-content-end mt-2 mb-4">
    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-x-circle me-1"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-success px-5">
        <i class="bi bi-check-circle-fill me-1"></i>Guardar Vehículo
    </button>
</div>

</form>
@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════
// VALIDACIÓN PATENTE
// ═══════════════════════════════════════════════════════════════
const inputPatente = document.getElementById('inputPatente');
const patenteMsg   = document.getElementById('patenteMsg');
const patenteIcon  = document.getElementById('patenteIcon');
const btnSubmit    = document.querySelector('button[type="submit"]');
const CHECK_URL    = "{{ route('vehicles.checkPatente') }}";

let patenteTimer = null;
let patenteOk    = null;

function setIconState(estado) {
    patenteIcon.className = '';
    if (estado === 'loading') patenteIcon.className = 'spinner-border spinner-border-sm text-secondary';
    else if (estado === 'ok')  patenteIcon.className = 'bi bi-check-circle-fill text-success fs-5';
    else if (estado === 'error') patenteIcon.className = 'bi bi-x-circle-fill text-danger fs-5';
    else patenteIcon.className = 'bi bi-dash text-secondary';
}

function mostrarMsg(texto, tipo) {
    patenteMsg.className = `small mt-1 text-${tipo}`;
    patenteMsg.innerHTML = texto;
}

async function verificarPatente(valor) {
    if (valor.length < 2) {
        patenteOk = null; setIconState('neutral');
        patenteMsg.className = 'd-none'; btnSubmit.disabled = false; return;
    }
    setIconState('loading');
    mostrarMsg('<i class="bi bi-hourglass-split me-1"></i>Verificando disponibilidad…', 'secondary');
    btnSubmit.disabled = true;
    try {
        const res  = await fetch(`${CHECK_URL}?patente=${encodeURIComponent(valor)}`);
        const data = await res.json();
        if (data.disponible === true) {
            patenteOk = true; setIconState('ok');
            mostrarMsg(`<i class="bi bi-check-circle-fill me-1"></i>${data.mensaje}`, 'success');
            btnSubmit.disabled = false;
        } else if (data.disponible === false) {
            patenteOk = false; setIconState('error');
            mostrarMsg(`<i class="bi bi-exclamation-triangle-fill me-1"></i>${data.mensaje}`, 'danger');
            btnSubmit.disabled = true;
        } else {
            patenteOk = null; setIconState('neutral');
            patenteMsg.className = 'd-none'; btnSubmit.disabled = false;
        }
    } catch { patenteOk = null; setIconState('neutral'); patenteMsg.className = 'd-none'; btnSubmit.disabled = false; }
}

inputPatente.addEventListener('input', function () {
    this.value = this.value.toUpperCase();
    clearTimeout(patenteTimer);
    if (this.value.length < 2) {
        patenteOk = null; setIconState('neutral'); patenteMsg.className = 'd-none'; btnSubmit.disabled = false; return;
    }
    patenteTimer = setTimeout(() => verificarPatente(this.value), 500);
});

document.getElementById('formVehiculo').addEventListener('submit', function (e) {
    if (inputPatente.value.trim().length > 0 && patenteOk === false) {
        e.preventDefault(); inputPatente.focus();
        mostrarMsg('<i class="bi bi-exclamation-triangle-fill me-1"></i>Corrija la patente antes de guardar.', 'danger');
    }
});

// ═══════════════════════════════════════════════════════════════
// CASCADA: Marca → Modelo (usando IDs)
// ═══════════════════════════════════════════════════════════════
const marcasConIds  = @json($marcasConIds);
const selectMarca   = document.getElementById('selectMarca');
const selectModelo  = document.getElementById('selectModelo');
const modeloHint    = document.getElementById('modeloHint');

function cargarModelos(marcaId, modeloSelId = '') {
    selectModelo.innerHTML = '';
    const marca = marcasConIds.find(m => m.id == marcaId);
    if (!marca) {
        selectModelo.add(new Option('— Seleccione primero la marca —', ''));
        selectModelo.disabled = true;
        modeloHint.textContent = 'Selecciona una marca para ver los modelos disponibles.';
        return;
    }
    selectModelo.add(new Option('— Seleccione modelo —', ''));
    marca.modelos.forEach(mo => {
        const opt = new Option(mo.nombre, mo.id);
        if (mo.id == modeloSelId) opt.selected = true;
        selectModelo.add(opt);
    });
    selectModelo.disabled = false;
    modeloHint.textContent = `${marca.modelos.length} modelos disponibles para ${marca.nombre}.`;
}

selectMarca.addEventListener('change', () => cargarModelos(selectMarca.value));

const marcaAnterior  = @json(old('marca_id', ''));
const modeloAnterior = @json(old('modelo_id', ''));
if (marcaAnterior) cargarModelos(marcaAnterior, modeloAnterior);

// ═══════════════════════════════════════════════════════════════
// CASCADA: Zona → Provincia → Municipio
// ═══════════════════════════════════════════════════════════════
const geoCascada       = @json($geoCascada);
const selectZona       = document.getElementById('selectZona');
const selectProvincia  = document.getElementById('selectProvincia');
const selectMunicipio  = document.getElementById('selectMunicipio');

function resetSelect(sel, placeholder) {
    sel.innerHTML = ''; sel.add(new Option(placeholder, '')); sel.disabled = true;
}

function cargarProvincias(zonaId, provSelId = '', munSelId = '') {
    resetSelect(selectProvincia, '— Seleccione Provincia —');
    resetSelect(selectMunicipio, '— Seleccione primero la Provincia —');
    if (!zonaId || !geoCascada[zonaId]) return;
    Object.entries(geoCascada[zonaId].provincias)
        .sort((a, b) => a[1].nombre.localeCompare(b[1].nombre))
        .forEach(([pid, prov]) => {
            const opt = new Option(prov.nombre, pid);
            if (pid == provSelId) opt.selected = true;
            selectProvincia.add(opt);
        });
    selectProvincia.disabled = false;
    if (provSelId) cargarMunicipios(zonaId, provSelId, munSelId);
}

function cargarMunicipios(zonaId, provinciaId, munSelId = '') {
    resetSelect(selectMunicipio, '— Seleccione Comuna —');
    const prov = geoCascada[zonaId]?.provincias?.[provinciaId];
    if (!prov) return;
    prov.municipios.sort((a, b) => a.nombre.localeCompare(b.nombre)).forEach(m => {
        const opt = new Option(m.nombre, m.id);
        if (m.id == munSelId) opt.selected = true;
        selectMunicipio.add(opt);
    });
    selectMunicipio.disabled = false;
}

selectZona.addEventListener('change', () => cargarProvincias(selectZona.value));
selectProvincia.addEventListener('change', () => cargarMunicipios(selectZona.value, selectProvincia.value));

const zonaAnterior      = @json(old('zona_id', ''));
const provinciaAnterior = @json(old('province_id', ''));
const municipioAnterior = @json(old('municipio_id', ''));
if (zonaAnterior) cargarProvincias(zonaAnterior, provinciaAnterior, municipioAnterior);

// ═══════════════════════════════════════════════════════════════
// Sección Agregado
// ═══════════════════════════════════════════════════════════════
function toggleAgregado(visible) {
    document.getElementById('seccionAgregado').style.display = visible ? '' : 'none';
}
</script>
@endpush
