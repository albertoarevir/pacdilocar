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
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150)->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->index('zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prefectures');
    }
};
