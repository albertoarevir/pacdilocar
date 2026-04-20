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
            $table->foreignId('vehicle_type_id')->constrained()->restrictOnDelete();
            $table->string('brand', 80)->nullable();
            $table->string('model', 80)->nullable();
            $table->foreignId('color_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('year')->nullable();
            $table->date('service_start_date')->nullable()->comment('Fecha de alta en servicio');
            $table->string('function', 255)->nullable()->comment('Función que desarrolla');
            $table->foreignId('fuel_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('engine_number', 100)->nullable();
            $table->string('chassis_number', 100)->nullable();
            $table->foreignId('funding_origin_id')->nullable()->constrained()->nullOnDelete();

            // Ubicación asignada
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('prefecture_id')->nullable()->constrained()->nullOnDelete()
                ->comment('Prefectura asignada');
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete()
                ->comment('Unidad asignada');

            // Agregación (vehículo prestado a otra unidad)
            $table->boolean('is_aggregated')->default(false);
            $table->foreignId('aggregate_prefecture_id')->nullable()
                ->constrained('prefectures')->nullOnDelete();
            $table->foreignId('aggregate_unit_id')->nullable()
                ->constrained('units')->nullOnDelete();

            // Estado operativo
            $table->enum('status', [
                'OPERATIVO',
                'PANNE',
                'MANTENIMIENTO',
                'BAJA',
                'FUERA_DE_SERVICIO',
                'ENAJENADO',
            ])->default('OPERATIVO')->index();

            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('vehicle_type_id');
            $table->index('prefecture_id');
            $table->index('unit_id');
            $table->index('zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
