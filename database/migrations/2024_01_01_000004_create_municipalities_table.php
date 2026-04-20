<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->restrictOnDelete();
            $table->foreignId('zona_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->string('nombre', 100);
            $table->timestamps();

            $table->unique(['province_id', 'nombre']);
            $table->index('province_id');
            $table->index('zona_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
