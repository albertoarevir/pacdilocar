<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique()
                ->comment('Clave interna: OPERATIVO, PANNE, etc.');
            $table->string('nombre', 80);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('genera_paralizado')->default(false)
                ->comment('Solo PANNE genera Downtime');
            $table->unsignedTinyInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_statuses');
    }
};
