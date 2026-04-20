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
            $table->string('code', 30)->unique()
                ->comment('Clave interna: OPERATIVO, PANNE, etc.');
            $table->string('name', 80);
            $table->string('description', 255)->nullable();
            $table->boolean('generates_downtime')->default(false)
                ->comment('Solo PANNE genera Downtime');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_statuses');
    }
};
