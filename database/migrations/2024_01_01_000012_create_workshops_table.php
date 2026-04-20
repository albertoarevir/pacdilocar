<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['interno', 'particular', 'zonal', 'otro'])->default('interno');
            $table->string('nombre', 150)->unique();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('persona_contacto', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
