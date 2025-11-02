<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('building_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time')->nullable();
            $table->time('time_ed')->nullable();
            $table->decimal('f', 5, 2)->nullable();
            $table->decimal('v1', 8, 2)->nullable();
            $table->decimal('v2', 8, 2)->nullable();
            $table->decimal('v3', 8, 2)->nullable();
            $table->decimal('a1', 8, 2)->nullable();
            $table->decimal('a2', 8, 2)->nullable();
            $table->decimal('a3', 8, 2)->nullable();
            $table->decimal('pf1', 5, 3)->nullable();
            $table->decimal('pf2', 5, 3)->nullable();
            $table->decimal('pf3', 5, 3)->nullable();
            $table->decimal('kwh', 10, 3)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('building_logs');
    }
};
