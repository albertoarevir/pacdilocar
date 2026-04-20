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
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['province_id', 'name']);
            $table->index('province_id');
            $table->index('zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
