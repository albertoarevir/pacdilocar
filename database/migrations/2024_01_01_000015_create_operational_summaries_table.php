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
         * Se recalcula mediante OperationalSummaryService::refresh($vehicleId).
         */
        Schema::create('operational_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->unique()->constrained()->cascadeOnDelete();

            $table->unsignedInteger('total_service_days')->default(0);
            $table->unsignedInteger('total_workshop_days')->default(0);
            $table->unsignedInteger('operational_days')->default(0);

            $table->decimal('availability_pct', 7, 4)->default(0)
                ->comment('Días operativos / días en servicio');
            $table->decimal('downtime_pct', 7, 4)->default(0)
                ->comment('Días en taller / días en servicio');

            $table->unsignedSmallInteger('workshop_entries')->default(0);
            $table->decimal('total_maintenance_cost', 14, 2)->default(0);

            // MTTR: tiempo promedio de reparación (días)
            $table->decimal('mttr_days', 7, 2)->default(0)
                ->comment('Mean Time To Repair = total_workshop_days / workshop_entries');

            $table->timestamp('last_computed_at')->nullable();
            $table->timestamps();

            $table->index('availability_pct');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_summaries');
    }
};
