<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->restrictOnDelete();
            $table->string('nombre', 100);
            $table->timestamps();

            $table->unique(['region_id', 'nombre']);
            $table->index('region_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
