<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Cache de indicadores operativos por vehículo.
         * Se recalcula mediante OperationalSummaryService::refresh($vehiculoId).
         */
        Schema::create('operational_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->unique()->constrained('vehicles')->cascadeOnDelete();

            $table->unsignedInteger('dias_servicio_total')->default(0);
            $table->unsignedInteger('dias_taller_total')->default(0);
            $table->unsignedInteger('dias_operativos')->default(0);

            $table->decimal('pct_disponibilidad', 7, 4)->default(0)
                ->comment('Días operativos / días en servicio');
            $table->decimal('pct_paralizado', 7, 4)->default(0)
                ->comment('Días en taller / días en servicio');

            $table->unsignedSmallInteger('ingresos_taller')->default(0);
            $table->decimal('costo_mantenimiento_total', 14, 2)->default(0);

            // MTTR: tiempo promedio de reparación (días)
            $table->decimal('dias_mttr', 7, 2)->default(0)
                ->comment('Mean Time To Repair = dias_taller_total / ingresos_taller');

            $table->timestamp('ultima_actualizacion')->nullable();
            $table->timestamps();

            $table->index('pct_disponibilidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_summaries');
    }
};
