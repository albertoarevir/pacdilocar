<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prefectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zona_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->string('nombre', 150)->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->index('zona_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prefectures');
    }
};
