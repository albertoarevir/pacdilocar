<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\FuelType;
use App\Models\FundingOrigin;
use App\Models\VehicleFunction;
use App\Models\VehicleModel;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /** Definición de cada catálogo: slug → config */
    private function catalogs(): array
    {
        return [
            'colores' => [
                'label'     => 'Colores',
                'labelSing' => 'Color',
                'model'     => Color::class,
                'icon'      => 'bi-palette-fill',
                'fields'    => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true, 'max' => 50],
                ],
                'rules'  => fn($id) => ['name' => ['required','string','max:50', \Illuminate\Validation\Rule::unique('colors','name')->ignore($id)]],
                'columns'   => ['Nombre'],
                'row'       => fn($r) => [$r->name],
            ],
            'tipos-vehiculo' => [
                'label'     => 'Tipos de Vehículo',
                'labelSing' => 'Tipo de Vehículo',
                'model'     => VehicleType::class,
                'icon'      => 'bi-truck-front-fill',
                'fields'    => [
                    ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true, 'max' => 10, 'upper' => true],
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true, 'max' => 80],
                ],
                'rules'  => fn($id) => [
                    'code' => ['required','string','max:10', \Illuminate\Validation\Rule::unique('vehicle_types','code')->ignore($id)],
                    'name' => ['required','string','max:80'],
                ],
                'columns'   => ['Código', 'Nombre'],
                'row'       => fn($r) => [$r->code, $r->name],
            ],
            'marcas' => [
                'label'     => 'Marcas',
                'labelSing' => 'Marca',
                'model'     => Brand::class,
                'icon'      => 'bi-bookmark-star-fill',
                'fields'    => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true, 'max' => 80],
                ],
                'rules'  => fn($id) => ['name' => ['required','string','max:80', \Illuminate\Validation\Rule::unique('brands','name')->ignore($id)]],
                'columns'   => ['Nombre'],
                'row'       => fn($r) => [$r->name],
            ],
            'modelos' => [
                'label'     => 'Modelos',
                'labelSing' => 'Modelo',
                'model'     => VehicleModel::class,
                'icon'      => 'bi-card-list',
                'fields'    => [
                    ['name' => 'brand_id', 'label' => 'Marca', 'type' => 'select', 'required' => true,
                     'options' => fn() => Brand::orderBy('name')->pluck('name','id')->toArray()],
                    ['name' => 'name', 'label' => 'Nombre del Modelo', 'type' => 'text', 'required' => true, 'max' => 100],
                ],
                'rules'  => fn($id) => [
                    'brand_id' => ['required','exists:brands,id'],
                    'name'     => ['required','string','max:100'],
                ],
                'columns'    => ['Marca', 'Modelo'],
                'row'        => fn($r) => [$r->brand?->name ?? '—', $r->name],
                'with'       => ['brand:id,name'],
                'orderBy'    => fn($q) => $q->join('brands','brands.id','=','vehicle_models.brand_id')
                                            ->orderBy('brands.name')->orderBy('vehicle_models.name')
                                            ->select('vehicle_models.*'),
            ],
            'combustibles' => [
                'label'     => 'Tipos de Combustible',
                'labelSing' => 'Combustible',
                'model'     => FuelType::class,
                'icon'      => 'bi-fuel-pump-fill',
                'fields'    => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true, 'max' => 50],
                ],
                'rules'  => fn($id) => ['name' => ['required','string','max:50', \Illuminate\Validation\Rule::unique('fuel_types','name')->ignore($id)]],
                'columns'   => ['Nombre'],
                'row'       => fn($r) => [$r->name],
            ],
            'estados' => [
                'label'     => 'Estados de Vehículo',
                'labelSing' => 'Estado',
                'model'     => VehicleStatus::class,
                'icon'      => 'bi-toggles',
                'fields'    => [
                    ['name' => 'code',             'label' => 'Código',          'type' => 'text',     'required' => true, 'max' => 30, 'upper' => true],
                    ['name' => 'name',             'label' => 'Nombre',          'type' => 'text',     'required' => true, 'max' => 80],
                    ['name' => 'description',      'label' => 'Descripción',     'type' => 'text',     'required' => false,'max' => 255],
                    ['name' => 'generates_downtime','label'=> 'Genera Downtime', 'type' => 'checkbox', 'required' => false],
                    ['name' => 'sort_order',       'label' => 'Orden',           'type' => 'number',   'required' => false,'min' => 0,'max' => 99],
                ],
                'rules'  => fn($id) => [
                    'code'              => ['required','string','max:30', \Illuminate\Validation\Rule::unique('vehicle_statuses','code')->ignore($id)],
                    'name'              => ['required','string','max:80'],
                    'description'       => ['nullable','string','max:255'],
                    'generates_downtime'=> ['boolean'],
                    'sort_order'        => ['nullable','integer','min:0','max:99'],
                ],
                'columns'    => ['Código', 'Nombre', 'Descripción', 'Genera Downtime', 'Orden'],
                'row'        => fn($r) => [
                    $r->code, $r->name, $r->description ?? '—',
                    $r->generates_downtime ? '<span class="badge bg-danger">Sí</span>' : '<span class="badge bg-secondary">No</span>',
                    $r->sort_order,
                ],
            ],
            'funciones' => [
                'label'     => 'Funciones que Desarrolla',
                'labelSing' => 'Función',
                'model'     => VehicleFunction::class,
                'icon'      => 'bi-diagram-3-fill',
                'fields'    => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true, 'max' => 100],
                ],
                'rules'  => fn($id) => ['name' => ['required','string','max:100', \Illuminate\Validation\Rule::unique('vehicle_functions','name')->ignore($id)]],
                'columns'   => ['Nombre'],
                'row'       => fn($r) => [$r->name],
            ],
            'financiamiento' => [
                'label'     => 'Origen de Financiamiento',
                'labelSing' => 'Origen',
                'model'     => FundingOrigin::class,
                'icon'      => 'bi-cash-coin',
                'fields'    => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true, 'max' => 100],
                ],
                'rules'  => fn($id) => ['name' => ['required','string','max:100', \Illuminate\Validation\Rule::unique('funding_origins','name')->ignore($id)]],
                'columns'   => ['Nombre'],
                'row'       => fn($r) => [$r->name],
            ],
        ];
    }

    /** Resuelve el catálogo o aborta 404 */
    private function resolve(string $catalog): array
    {
        $catalogs = $this->catalogs();
        abort_unless(isset($catalogs[$catalog]), 404);
        return [$catalogs[$catalog], $catalog, $this->catalogs()];
    }

    public function index(string $catalog): View
    {
        [$cfg, $slug, $all] = $this->resolve($catalog);

        /** @var Model $modelClass */
        $modelClass = $cfg['model'];
        $query = $modelClass::query();

        if (isset($cfg['with'])) {
            $query->with($cfg['with']);
        }

        if (isset($cfg['orderBy'])) {
            $items = ($cfg['orderBy'])($query)->get();
        } else {
            $items = $query->orderBy('id')->get();
        }

        // Preparar los datos de campos que son selects (para el modal)
        $fields = $this->resolveFieldOptions($cfg['fields']);

        return view('config.catalog', compact('cfg', 'slug', 'all', 'items', 'fields'));
    }

    public function store(Request $request, string $catalog): RedirectResponse
    {
        [$cfg] = $this->resolve($catalog);

        $data = $request->validate(($cfg['rules'])(null));
        if (isset($data['generates_downtime'])) {
            $data['generates_downtime'] = (bool) $data['generates_downtime'];
        }
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $cfg['model']::create($data);

        return redirect()->route('config.index', $catalog)
            ->with('success', "{$cfg['labelSing']} creado correctamente.");
    }

    public function update(Request $request, string $catalog, int $id): RedirectResponse
    {
        [$cfg] = $this->resolve($catalog);

        $item = $cfg['model']::findOrFail($id);
        $data = $request->validate(($cfg['rules'])($id));

        if (isset($data['generates_downtime'])) {
            $data['generates_downtime'] = (bool) $data['generates_downtime'];
        }
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $item->update($data);

        return redirect()->route('config.index', $catalog)
            ->with('success', "{$cfg['labelSing']} actualizado correctamente.");
    }

    public function destroy(string $catalog, int $id): RedirectResponse
    {
        [$cfg] = $this->resolve($catalog);

        $item = $cfg['model']::findOrFail($id);

        // Protección básica: no borrar si tiene vehículos asociados
        if (method_exists($item, 'vehicles') && $item->vehicles()->exists()) {
            return redirect()->route('config.index', $catalog)
                ->with('error', "No se puede eliminar: hay vehículos asociados a este registro.");
        }

        // Para modelos (VehicleModel), verificar vehículos
        if ($cfg['model'] === VehicleModel::class && $item->vehicles()->exists()) {
            return redirect()->route('config.index', $catalog)
                ->with('error', "No se puede eliminar: hay vehículos que usan este modelo.");
        }

        // Para marcas, verificar modelos y vehículos hijos
        if ($cfg['model'] === Brand::class) {
            if ($item->vehicleModels()->exists() || $item->vehicles()->exists()) {
                return redirect()->route('config.index', $catalog)
                    ->with('error', "No se puede eliminar: la marca tiene modelos o vehículos asociados.");
            }
        }

        $item->delete();

        return redirect()->route('config.index', $catalog)
            ->with('success', "{$cfg['labelSing']} eliminado correctamente.");
    }

    private function resolveFieldOptions(array $fields): array
    {
        return array_map(function ($field) {
            if ($field['type'] === 'select' && isset($field['options']) && is_callable($field['options'])) {
                $field['options'] = ($field['options'])();
            }
            return $field;
        }, $fields);
    }
}
