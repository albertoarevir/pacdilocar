<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('patente', 20)->unique()->comment('Patente o sigla del vehículo');
            $table->foreignId('tipo_vehiculo_id')->constrained('vehicle_types')->restrictOnDelete();
            $table->string('marca', 80)->nullable();
            $table->string('modelo', 80)->nullable();
            $table->foreignId('color_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('anio')->nullable();
            $table->date('fecha_inicio_servicio')->nullable()->comment('Fecha de alta en servicio');
            $table->string('funcion', 255)->nullable()->comment('Función que desarrolla');
            $table->foreignId('tipo_combustible_id')->nullable()->constrained('fuel_types')->nullOnDelete();
            $table->string('numero_motor', 100)->nullable();
            $table->string('numero_chasis', 100)->nullable();
            $table->foreignId('origen_financiamiento_id')->nullable()->constrained('funding_origins')->nullOnDelete();

            // Ubicación asignada
            $table->foreignId('zona_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('municipio_id')->nullable()->constrained('municipalities')->nullOnDelete();
            $table->foreignId('prefectura_id')->nullable()->constrained('prefectures')->nullOnDelete()
                ->comment('Prefectura asignada');
            $table->foreignId('unidad_id')->nullable()->constrained('units')->nullOnDelete()
                ->comment('Unidad asignada');

            // Agregación (vehículo prestado a otra unidad)
            $table->boolean('es_agregado')->default(false);
            $table->foreignId('prefectura_agregado_id')->nullable()
                ->constrained('prefectures')->nullOnDelete();
            $table->foreignId('unidad_agregado_id')->nullable()
                ->constrained('units')->nullOnDelete();

            // Estado operativo
            $table->enum('estado', [
                'OPERATIVO',
                'PANNE',
                'MANTENIMIENTO',
                'BAJA',
                'FUERA_DE_SERVICIO',
                'ENAJENADO',
            ])->default('OPERATIVO')->index();

            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tipo_vehiculo_id');
            $table->index('prefectura_id');
            $table->index('unidad_id');
            $table->index('zona_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
