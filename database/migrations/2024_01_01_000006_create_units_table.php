<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Unidades y Destacamentos se unifican en esta tabla.
         * tipo distingue entre 'unidad' y 'destacamento'.
         * unidad_padre_id permite que un Destacamento pertenezca a una Unidad.
         */
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefectura_id')->nullable()->constrained('prefectures')->nullOnDelete();
            $table->foreignId('unidad_padre_id')->nullable()->constrained('units')->nullOnDelete();
            $table->enum('tipo', ['unidad', 'destacamento'])->default('unidad');
            $table->string('nombre', 150);
            $table->timestamps();
            $table->softDeletes();

            $table->index('prefectura_id');
            $table->index('unidad_padre_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
