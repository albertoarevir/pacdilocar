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
         * type distingue entre 'unidad' y 'destacamento'.
         * parent_id permite que un Destacamento pertenezca a una Unidad.
         */
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefecture_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('units')->nullOnDelete();
            $table->enum('type', ['unidad', 'destacamento'])->default('unidad');
            $table->string('name', 150);
            $table->timestamps();
            $table->softDeletes();

            $table->index('prefecture_id');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
