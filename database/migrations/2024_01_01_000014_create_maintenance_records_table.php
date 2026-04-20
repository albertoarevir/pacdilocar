<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('workshop_id')->nullable()->constrained()->nullOnDelete();

            $table->date('entry_date')->comment('Fecha ingreso al taller');
            $table->date('exit_date')->nullable()->comment('Fecha salida del taller');
            $table->unsignedSmallInteger('downtime_days')->nullable()
                ->comment('Días en taller = exit_date - entry_date');

            $table->enum('record_status', ['Abierto', 'Cerrado', 'En Diagnóstico'])
                ->default('Abierto')->index();
            $table->enum('maintenance_type', ['Correctivo', 'Preventivo', 'Emergencia'])
                ->index();

            $table->text('technical_description')->nullable();
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->unsignedInteger('mileage_entry')->nullable()->comment('Kilometraje al ingreso');
            $table->string('work_order_number', 50)->nullable()->unique();
            $table->text('observations')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('vehicle_id');
            $table->index('entry_date');
            $table->index('maintenance_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
