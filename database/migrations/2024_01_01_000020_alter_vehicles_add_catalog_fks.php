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
            if (! Schema::hasColumn('vehicles', 'marca_id')) {
                $table->foreignId('marca_id')
                    ->nullable()->after('numero_chasis')
                    ->constrained('brands')->nullOnDelete();
            }
            if (! Schema::hasColumn('vehicles', 'modelo_id')) {
                $table->foreignId('modelo_id')
                    ->nullable()->after('marca_id')
                    ->constrained('vehicle_models')->nullOnDelete();
            }
            if (! Schema::hasColumn('vehicles', 'estado_vehiculo_id')) {
                $table->foreignId('estado_vehiculo_id')
                    ->nullable()->after('modelo_id')
                    ->constrained('vehicle_statuses')->restrictOnDelete();
            }
            if (! Schema::hasColumn('vehicles', 'funcion_id')) {
                $table->foreignId('funcion_id')
                    ->nullable()->after('estado_vehiculo_id')
                    ->constrained('vehicle_functions')->nullOnDelete();
            }
        });

        // ── 2. Migrar datos: marca (string) → marca_id ────────────────────────────
        $marcasExistentes = DB::table('vehicles')
            ->whereNotNull('marca')
            ->distinct()
            ->pluck('marca');

        foreach ($marcasExistentes as $nombreMarca) {
            DB::table('brands')->insertOrIgnore([
                'nombre'     => $nombreMarca,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $marcaId = DB::table('brands')->where('nombre', $nombreMarca)->value('id');

            // Migrar modelos de esa marca
            $modelosExistentes = DB::table('vehicles')
                ->where('marca', $nombreMarca)
                ->whereNotNull('modelo')
                ->distinct()
                ->pluck('modelo');

            foreach ($modelosExistentes as $nombreModelo) {
                DB::table('vehicle_models')->insertOrIgnore([
                    'marca_id'   => $marcaId,
                    'nombre'     => $nombreModelo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $modeloId = DB::table('vehicle_models')
                    ->where('marca_id', $marcaId)
                    ->where('nombre', $nombreModelo)
                    ->value('id');
                DB::table('vehicles')
                    ->where('marca', $nombreMarca)
                    ->where('modelo', $nombreModelo)
                    ->update(['marca_id' => $marcaId, 'modelo_id' => $modeloId]);
            }

            // Vehículos con marca pero sin modelo
            DB::table('vehicles')
                ->where('marca', $nombreMarca)
                ->whereNull('modelo')
                ->update(['marca_id' => $marcaId]);
        }

        // ── 3. Migrar datos: estado (string) → estado_vehiculo_id ─────────────────
        DB::table('vehicles')->orderBy('id')->each(function ($v) {
            $estadoId = DB::table('vehicle_statuses')
                ->where('codigo', $v->estado)
                ->value('id');
            if ($estadoId) {
                DB::table('vehicles')
                    ->where('id', $v->id)
                    ->update(['estado_vehiculo_id' => $estadoId]);
            }
        });

        // ── 4. Migrar datos: funcion (string) → funcion_id ─────────────────────
        $funcionesExistentes = DB::table('vehicles')
            ->whereNotNull('funcion')
            ->distinct()
            ->pluck('funcion');

        foreach ($funcionesExistentes as $nombreFuncion) {
            $fnId = DB::table('vehicle_functions')
                ->where('nombre', $nombreFuncion)
                ->value('id');

            if (! $fnId) {
                $fnId = DB::table('vehicle_functions')->insertGetId([
                    'nombre'     => $nombreFuncion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('vehicles')
                ->where('funcion', $nombreFuncion)
                ->update(['funcion_id' => $fnId]);
        }

        // ── 5. Hacer estado_vehiculo_id NOT NULL (todos tienen valor ahora) ────────
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_vehiculo_id')->nullable(false)->change();
        });

        // ── 6. Eliminar columnas string reemplazadas ──────────────────────────────
        $eliminar = array_filter(['marca', 'modelo', 'estado', 'funcion'], fn($col) => Schema::hasColumn('vehicles', $col));
        if ($eliminar) {
            Schema::table('vehicles', function (Blueprint $table) use ($eliminar) {
                $table->dropColumn(array_values($eliminar));
            });
        }
    }

    public function down(): void
    {
        // Restaurar columnas string
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('marca', 80)->nullable()->after('numero_chasis');
            $table->string('modelo', 80)->nullable()->after('marca');
            $table->enum('estado', [
                'OPERATIVO', 'PANNE', 'MANTENIMIENTO',
                'BAJA', 'FUERA_DE_SERVICIO', 'ENAJENADO',
            ])->default('OPERATIVO')->after('modelo');
            $table->string('funcion', 255)->nullable()->after('estado');
        });

        // Restaurar datos desde FKs
        DB::table('vehicles')->orderBy('id')->each(function ($v) {
            $marca  = DB::table('brands')->where('id', $v->marca_id)->value('nombre');
            $modelo = DB::table('vehicle_models')->where('id', $v->modelo_id)->value('nombre');
            $estado = DB::table('vehicle_statuses')->where('id', $v->estado_vehiculo_id)->value('codigo');
            $fn     = DB::table('vehicle_functions')->where('id', $v->funcion_id)->value('nombre');
            DB::table('vehicles')->where('id', $v->id)
                ->update(compact('marca', 'modelo', 'estado') + ['funcion' => $fn]);
        });

        // Eliminar columnas FK
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['marca_id']);
            $table->dropForeign(['modelo_id']);
            $table->dropForeign(['estado_vehiculo_id']);
            $table->dropForeign(['funcion_id']);
            $table->dropColumn(['marca_id', 'modelo_id', 'estado_vehiculo_id', 'funcion_id']);
        });
    }
};
