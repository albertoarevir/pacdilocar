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

            $table->foreignId('vehiculo_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('maintenance_categories')->nullOnDelete();
            $table->foreignId('taller_id')->nullable()->constrained('workshops')->nullOnDelete();

            $table->date('fecha_ingreso')->comment('Fecha ingreso al taller');
            $table->date('fecha_salida')->nullable()->comment('Fecha salida del taller');
            $table->unsignedSmallInteger('dias_paralizado')->nullable()
                ->comment('Días en taller = fecha_salida - fecha_ingreso');

            $table->enum('estado', ['Abierto', 'Cerrado', 'En Diagnóstico'])
                ->default('Abierto')->index();
            $table->enum('tipo_mantenimiento', ['Correctivo', 'Preventivo', 'Emergencia'])
                ->index();

            $table->text('descripcion_tecnica')->nullable();
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->unsignedInteger('kilometraje_ingreso')->nullable()->comment('Kilometraje al ingreso');
            $table->string('numero_orden', 50)->nullable()->unique();
            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('vehiculo_id');
            $table->index('fecha_ingreso');
            $table->index('categoria_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
