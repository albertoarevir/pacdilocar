@extends('layouts.app')
@section('title', 'Editar Vehículo ' . $vehicle->patente)
@section('page-title', '✏️ Editar Vehículo: ' . $vehicle->patente)

@section('content')

<form method="POST" action="{{ route('vehicles.update', $vehicle) }}" novalidate id="formVehiculo">
@csrf
@method('PUT')

<div class="row g-3">

    {{-- ── COLUMNA IZQUIERDA ─────────────────────────────────────────── --}}
    <div class="col-lg-6">

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
                                value="{{ old('patente', $vehicle->patente) }}"
                                maxlength="20" autocomplete="off"
                                style="text-transform:uppercase"
                                data-original="{{ $vehicle->patente }}">
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
                        <select name="vehicle_type_id"
                            class="form-select @error('vehicle_type_id') is-invalid @enderror">
                            <option value="">— Seleccione —</option>
                            @foreach($vehicleTypes as $vt)
                                <option value="{{ $vt->id }}"
                                    {{ old('vehicle_type_id', $vehicle->vehicle_type_id) == $vt->id ? 'selected' : '' }}>
                                    [{{ $vt->code }}] {{ $vt->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Marca</label>
                        <select name="brand_id" id="selectBrand"
                            class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">— Seleccione marca —</option>
                            @foreach($brandsWithIds as $b)
                                <option value="{{ $b['id'] }}"
                                    {{ old('brand_id', $vehicle->brand_id) == $b['id'] ? 'selected' : '' }}>
                                    {{ $b['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Modelo</label>
                        <select name="vehicle_model_id" id="selectModel"
                            class="form-select @error('vehicle_model_id') is-invalid @enderror"
                            disabled>
                            <option value="">— Seleccione primero la marca —</option>
                        </select>
                        @error('vehicle_model_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="modelHint">
                            Selecciona una marca para ver los modelos disponibles.
                        </small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Año</label>
                        <input type="number" name="year"
                            class="form-control @error('year') is-invalid @enderror"
                            value="{{ old('year', $vehicle->year) }}"
                            min="1950" max="{{ date('Y') + 1 }}">
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Color</label>
                        <select name="color_id"
                            class="form-select @error('color_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($colors as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('color_id', $vehicle->color_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('color_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Combustible</label>
                        <select name="fuel_type_id"
                            class="form-select @error('fuel_type_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($fuelTypes as $ft)
                                <option value="{{ $ft->id }}"
                                    {{ old('fuel_type_id', $vehicle->fuel_type_id) == $ft->id ? 'selected' : '' }}>
                                    {{ $ft->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fuel_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">N° Motor</label>
                        <input type="text" name="engine_number"
                            class="form-control @error('engine_number') is-invalid @enderror"
                            value="{{ old('engine_number', $vehicle->engine_number) }}" maxlength="100">
                        @error('engine_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">N° Chasis</label>
                        <input type="text" name="chassis_number"
                            class="form-control @error('chassis_number') is-invalid @enderror"
                            value="{{ old('chassis_number', $vehicle->chassis_number) }}" maxlength="100">
                        @error('chassis_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha Alta en Servicio</label>
                        <input type="date" name="service_start_date"
                            class="form-control @error('service_start_date') is-invalid @enderror"
                            value="{{ old('service_start_date', $vehicle->service_start_date?->format('Y-m-d')) }}">
                        @error('service_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Estado <span class="text-danger">*</span>
                        </label>
                        <select name="vehicle_status_id"
                            class="form-select @error('vehicle_status_id') is-invalid @enderror">
                            <option value="">— Seleccione —</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('vehicle_status_id', $vehicle->vehicle_status_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_status_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Función que Desarrolla</label>
                        <select name="vehicle_function_id"
                            class="form-select @error('vehicle_function_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($functions as $fn)
                                <option value="{{ $fn->id }}"
                                    {{ old('vehicle_function_id', $vehicle->vehicle_function_id) == $fn->id ? 'selected' : '' }}>
                                    {{ $fn->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_function_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Origen Financiamiento</label>
                        <select name="funding_origin_id"
                            class="form-select @error('funding_origin_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($fundingOrigins as $fo)
                                <option value="{{ $fo->id }}"
                                    {{ old('funding_origin_id', $vehicle->funding_origin_id) == $fo->id ? 'selected' : '' }}>
                                    {{ $fo->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('funding_origin_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- ── COLUMNA DERECHA ──────────────────────────────────────────── --}}
    <div class="col-lg-6">

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold py-2">
                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>Ubicación y Asignación
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label fw-semibold">Jefatura de Zona</label>
                        <select name="zone_id" id="selectZone"
                            class="form-select @error('zone_id') is-invalid @enderror">
                            <option value="">— Seleccione Zona —</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}"
                                    {{ old('zone_id', $vehicle->zone_id) == $z->id ? 'selected' : '' }}>
                                    {{ $z->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('zone_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Provincia</label>
                        <select name="province_id" id="selectProvince"
                            class="form-select @error('province_id') is-invalid @enderror" disabled>
                            <option value="">— Seleccione primero la Zona —</option>
                        </select>
                        @error('province_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Comuna</label>
                        <select name="municipality_id" id="selectMunicipality"
                            class="form-select @error('municipality_id') is-invalid @enderror" disabled>
                            <option value="">— Seleccione primero la Provincia —</option>
                        </select>
                        @error('municipality_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prefectura Asignada</label>
                        <select name="prefecture_id"
                            class="form-select @error('prefecture_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($prefectures as $pf)
                                <option value="{{ $pf->id }}"
                                    {{ old('prefecture_id', $vehicle->prefecture_id) == $pf->id ? 'selected' : '' }}>
                                    {{ $pf->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('prefecture_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad Asignada</label>
                        <select name="unit_id"
                            class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">— Sin especificar —</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('unit_id', $vehicle->unit_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
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
                        name="is_aggregated" id="is_aggregated" value="1"
                        {{ old('is_aggregated', $vehicle->is_aggregated) ? 'checked' : '' }}
                        onchange="toggleAgregado(this.checked)">
                    <label class="form-check-label fw-semibold" for="is_aggregated">
                        Este vehículo está agregado a otra unidad
                    </label>
                </div>

                @php $showAgg = old('is_aggregated', $vehicle->is_aggregated); @endphp
                <div id="seccionAgregado" style="{{ $showAgg ? '' : 'display:none' }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Prefectura Agregado</label>
                            <select name="aggregate_prefecture_id"
                                class="form-select @error('aggregate_prefecture_id') is-invalid @enderror">
                                <option value="">— Sin especificar —</option>
                                @foreach($prefectures as $pf)
                                    <option value="{{ $pf->id }}"
                                        {{ old('aggregate_prefecture_id', $vehicle->aggregate_prefecture_id) == $pf->id ? 'selected' : '' }}>
                                        {{ $pf->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('aggregate_prefecture_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unidad/Dest. Agregado</label>
                            <select name="aggregate_unit_id"
                                class="form-select @error('aggregate_unit_id') is-invalid @enderror">
                                <option value="">— Sin especificar —</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}"
                                        {{ old('aggregate_unit_id', $vehicle->aggregate_unit_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('aggregate_unit_id')
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
                <textarea name="observations" rows="3"
                    class="form-control @error('observations') is-invalid @enderror"
                    placeholder="Observaciones adicionales del vehículo..."
                    maxlength="1000">{{ old('observations', $vehicle->observations) }}</textarea>
                @error('observations')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Máximo 1000 caracteres.</small>
            </div>
        </div>

    </div>
</div>

{{-- Botones --}}
<div class="d-flex gap-2 justify-content-end mt-2 mb-4">
    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-x-circle me-1"></i>Cancelar
    </a>
    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-primary px-4">
        <i class="bi bi-eye me-1"></i>Ver Detalle
    </a>
    <button type="submit" class="btn btn-warning px-5">
        <i class="bi bi-check-circle-fill me-1"></i>Guardar Cambios
    </button>
</div>

</form>
@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════
// VALIDACIÓN PATENTE (ignora la patente actual del vehículo)
// ═══════════════════════════════════════════════════════════════
const inputPatente   = document.getElementById('inputPatente');
const patenteMsg     = document.getElementById('patenteMsg');
const patenteIcon    = document.getElementById('patenteIcon');
const btnSubmit      = document.querySelector('button[type="submit"]');
const CHECK_URL      = "{{ route('vehicles.checkPatente') }}";
const ORIGINAL_PAT   = inputPatente.dataset.original.toUpperCase();

let patenteTimer = null;
let patenteOk    = true; // starts OK because current patente is valid

function setIconState(state) {
    patenteIcon.className = '';
    if (state === 'loading') patenteIcon.className = 'spinner-border spinner-border-sm text-secondary';
    else if (state === 'ok')    patenteIcon.className = 'bi bi-check-circle-fill text-success fs-5';
    else if (state === 'error') patenteIcon.className = 'bi bi-x-circle-fill text-danger fs-5';
    else patenteIcon.className = 'bi bi-dash text-secondary';
}

function showMsg(text, type) {
    patenteMsg.className = `small mt-1 text-${type}`;
    patenteMsg.innerHTML = text;
}

async function checkPatente(value) {
    // Si es la misma patente original no verificar
    if (value === ORIGINAL_PAT) {
        patenteOk = true; setIconState('ok');
        patenteMsg.className = 'd-none'; btnSubmit.disabled = false; return;
    }
    if (value.length < 2) {
        patenteOk = null; setIconState('neutral');
        patenteMsg.className = 'd-none'; btnSubmit.disabled = false; return;
    }
    setIconState('loading');
    showMsg('<i class="bi bi-hourglass-split me-1"></i>Verificando disponibilidad…', 'secondary');
    btnSubmit.disabled = true;
    try {
        const res  = await fetch(`${CHECK_URL}?patente=${encodeURIComponent(value)}`);
        const data = await res.json();
        if (data.available === true) {
            patenteOk = true; setIconState('ok');
            showMsg(`<i class="bi bi-check-circle-fill me-1"></i>${data.message}`, 'success');
            btnSubmit.disabled = false;
        } else if (data.available === false) {
            patenteOk = false; setIconState('error');
            showMsg(`<i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}`, 'danger');
            btnSubmit.disabled = true;
        } else {
            patenteOk = true; setIconState('neutral');
            patenteMsg.className = 'd-none'; btnSubmit.disabled = false;
        }
    } catch { patenteOk = true; setIconState('neutral'); patenteMsg.className = 'd-none'; btnSubmit.disabled = false; }
}

inputPatente.addEventListener('input', function () {
    this.value = this.value.toUpperCase();
    clearTimeout(patenteTimer);
    patenteTimer = setTimeout(() => checkPatente(this.value), 500);
});

document.getElementById('formVehiculo').addEventListener('submit', function (e) {
    if (patenteOk === false) {
        e.preventDefault(); inputPatente.focus();
        showMsg('<i class="bi bi-exclamation-triangle-fill me-1"></i>Corrija la patente antes de guardar.', 'danger');
    }
});

// ═══════════════════════════════════════════════════════════════
// CASCADA: Marca → Modelo
// ═══════════════════════════════════════════════════════════════
const brandsWithIds = @json($brandsWithIds);
const selectBrand   = document.getElementById('selectBrand');
const selectModel   = document.getElementById('selectModel');
const modelHint     = document.getElementById('modelHint');

function populateModels(brandId, selectedModelId = '') {
    selectModel.innerHTML = '';
    const brand = brandsWithIds.find(b => b.id == brandId);
    if (!brand) {
        selectModel.add(new Option('— Seleccione primero la marca —', ''));
        selectModel.disabled = true;
        modelHint.textContent = 'Selecciona una marca para ver los modelos disponibles.';
        return;
    }
    selectModel.add(new Option('— Seleccione modelo —', ''));
    brand.models.forEach(m => {
        const opt = new Option(m.name, m.id);
        if (m.id == selectedModelId) opt.selected = true;
        selectModel.add(opt);
    });
    selectModel.disabled = false;
    modelHint.textContent = `${brand.models.length} modelos disponibles para ${brand.name}.`;
}

selectBrand.addEventListener('change', () => populateModels(selectBrand.value));

// Restaurar marca y modelo al cargar la página
const initBrandId = @json(old('brand_id', $vehicle->brand_id));
const initModelId = @json(old('vehicle_model_id', $vehicle->vehicle_model_id));
if (initBrandId) populateModels(initBrandId, initModelId);

// ═══════════════════════════════════════════════════════════════
// CASCADA: Zona → Provincia → Comuna
// ═══════════════════════════════════════════════════════════════
const geoCascade         = @json($geoCascade);
const selectZone         = document.getElementById('selectZone');
const selectProvince     = document.getElementById('selectProvince');
const selectMunicipality = document.getElementById('selectMunicipality');

function resetSelect(sel, placeholder) {
    sel.innerHTML = ''; sel.add(new Option(placeholder, '')); sel.disabled = true;
}

function populateProvinces(zoneId, selProvId = '', selMunId = '') {
    resetSelect(selectProvince, '— Seleccione Provincia —');
    resetSelect(selectMunicipality, '— Seleccione primero la Provincia —');
    if (!zoneId || !geoCascade[zoneId]) return;
    Object.entries(geoCascade[zoneId].provinces)
        .sort((a, b) => a[1].name.localeCompare(b[1].name))
        .forEach(([pid, prov]) => {
            const opt = new Option(prov.name, pid);
            if (pid == selProvId) opt.selected = true;
            selectProvince.add(opt);
        });
    selectProvince.disabled = false;
    if (selProvId) populateMunicipalities(zoneId, selProvId, selMunId);
}

function populateMunicipalities(zoneId, provinceId, selMunId = '') {
    resetSelect(selectMunicipality, '— Seleccione Comuna —');
    const prov = geoCascade[zoneId]?.provinces?.[provinceId];
    if (!prov) return;
    prov.municipalities.sort((a, b) => a.name.localeCompare(b.name)).forEach(m => {
        const opt = new Option(m.name, m.id);
        if (m.id == selMunId) opt.selected = true;
        selectMunicipality.add(opt);
    });
    selectMunicipality.disabled = false;
}

selectZone.addEventListener('change', () => populateProvinces(selectZone.value));
selectProvince.addEventListener('change', () => populateMunicipalities(selectZone.value, selectProvince.value));

// Restaurar zona/provincia/comuna al cargar
const initZoneId = @json(old('zone_id', $vehicle->zone_id));
const initProvId = @json(old('province_id', $vehicle->province_id));
const initMunId  = @json(old('municipality_id', $vehicle->municipality_id));
if (initZoneId) populateProvinces(initZoneId, initProvId, initMunId);

// ═══════════════════════════════════════════════════════════════
// Sección Agregado
// ═══════════════════════════════════════════════════════════════
function toggleAgregado(visible) {
    document.getElementById('seccionAgregado').style.display = visible ? '' : 'none';
}
</script>
@endpush
