<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Agregar columnas FK (nullable para no romper registros existentes) ──
        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'brand_id')) {
                $table->foreignId('brand_id')
                    ->nullable()->after('chassis_number')
                    ->constrained('brands')->nullOnDelete();
            }
            if (! Schema::hasColumn('vehicles', 'vehicle_model_id')) {
                $table->foreignId('vehicle_model_id')
                    ->nullable()->after('brand_id')
                    ->constrained('vehicle_models')->nullOnDelete();
            }
            if (! Schema::hasColumn('vehicles', 'vehicle_status_id')) {
                $table->foreignId('vehicle_status_id')
                    ->nullable()->after('vehicle_model_id')
                    ->constrained('vehicle_statuses')->restrictOnDelete();
            }
            if (! Schema::hasColumn('vehicles', 'vehicle_function_id')) {
                $table->foreignId('vehicle_function_id')
                    ->nullable()->after('vehicle_status_id')
                    ->constrained('vehicle_functions')->nullOnDelete();
            }
        });

        // ── 2. Migrar datos: brand (string) → brand_id ────────────────────────────
        $existingBrands = DB::table('vehicles')
            ->whereNotNull('brand')
            ->distinct()
            ->pluck('brand');

        foreach ($existingBrands as $brandName) {
            DB::table('brands')->insertOrIgnore([
                'name'       => $brandName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $brandId = DB::table('brands')->where('name', $brandName)->value('id');

            // Migrar modelos de esa marca
            $existingModels = DB::table('vehicles')
                ->where('brand', $brandName)
                ->whereNotNull('model')
                ->distinct()
                ->pluck('model');

            foreach ($existingModels as $modelName) {
                DB::table('vehicle_models')->insertOrIgnore([
                    'brand_id'   => $brandId,
                    'name'       => $modelName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $modelId = DB::table('vehicle_models')->where('brand_id', $brandId)->where('name', $modelName)->value('id');
                DB::table('vehicles')
                    ->where('brand', $brandName)
                    ->where('model', $modelName)
                    ->update(['brand_id' => $brandId, 'vehicle_model_id' => $modelId]);
            }

            // Vehículos con marca pero sin modelo
            DB::table('vehicles')
                ->where('brand', $brandName)
                ->whereNull('model')
                ->update(['brand_id' => $brandId]);
        }

        // ── 3. Migrar datos: status (string) → vehicle_status_id ─────────────────
        DB::table('vehicles')->orderBy('id')->each(function ($v) {
            $statusId = DB::table('vehicle_statuses')
                ->where('code', $v->status)
                ->value('id');
            if ($statusId) {
                DB::table('vehicles')
                    ->where('id', $v->id)
                    ->update(['vehicle_status_id' => $statusId]);
            }
        });

        // ── 4. Migrar datos: function (string) → vehicle_function_id ─────────────
        $existingFunctions = DB::table('vehicles')
            ->whereNotNull('function')
            ->distinct()
            ->pluck('function');

        foreach ($existingFunctions as $fnName) {
            $fnId = DB::table('vehicle_functions')
                ->where('name', $fnName)
                ->value('id');

            if (! $fnId) {
                $fnId = DB::table('vehicle_functions')->insertGetId([
                    'name'       => $fnName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('vehicles')
                ->where('function', $fnName)
                ->update(['vehicle_function_id' => $fnId]);
        }

        // ── 5. Hacer vehicle_status_id NOT NULL (todos tienen valor ahora) ────────
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_status_id')->nullable(false)->change();
        });

        // ── 6. Eliminar columnas string reemplazadas ──────────────────────────────
        $toDrop = array_filter(['brand', 'model', 'status', 'function'], fn($col) => Schema::hasColumn('vehicles', $col));
        if ($toDrop) {
            Schema::table('vehicles', function (Blueprint $table) use ($toDrop) {
                $table->dropColumn(array_values($toDrop));
            });
        }
    }

    public function down(): void
    {
        // Restaurar columnas string
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('brand', 80)->nullable()->after('chassis_number');
            $table->string('model', 80)->nullable()->after('brand');
            $table->enum('status', [
                'OPERATIVO', 'PANNE', 'MANTENIMIENTO',
                'BAJA', 'FUERA_DE_SERVICIO', 'ENAJENADO',
            ])->default('OPERATIVO')->after('model');
            $table->string('function', 255)->nullable()->after('status');
        });

        // Restaurar datos desde FKs
        DB::table('vehicles')->orderBy('id')->each(function ($v) {
            $brand  = DB::table('brands')->where('id', $v->brand_id)->value('name');
            $model  = DB::table('vehicle_models')->where('id', $v->vehicle_model_id)->value('name');
            $status = DB::table('vehicle_statuses')->where('id', $v->vehicle_status_id)->value('code');
            $fn     = DB::table('vehicle_functions')->where('id', $v->vehicle_function_id)->value('name');
            DB::table('vehicles')->where('id', $v->id)
                ->update(compact('brand', 'model', 'status') + ['function' => $fn]);
        });

        // Eliminar columnas FK
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['vehicle_model_id']);
            $table->dropForeign(['vehicle_status_id']);
            $table->dropForeign(['vehicle_function_id']);
            $table->dropColumn(['brand_id', 'vehicle_model_id', 'vehicle_status_id', 'vehicle_function_id']);
        });
    }
};
