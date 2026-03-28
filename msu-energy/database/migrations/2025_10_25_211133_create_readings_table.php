<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained()->cascadeOnDelete();
            $table->decimal('f', 6, 2)->nullable();
            $table->decimal('v1',8,2)->nullable();
            $table->decimal('v2',8,2)->nullable();
            $table->decimal('v3',8,2)->nullable();
            $table->decimal('a1',8,2)->nullable();
            $table->decimal('a2',8,2)->nullable();
            $table->decimal('a3',8,2)->nullable();
            $table->decimal('kw1', 10,3)->nullable();
            $table->decimal('kw2', 10,3)->nullable();
            $table->decimal('kw3', 10,3)->nullable();
            $table->decimal('pf1', 5,3)->nullable();
            $table->decimal('pf2', 5,3)->nullable();
            $table->decimal('pf3', 5,3)->nullable();
            $table->decimal('pfiii', 5,3)->nullable();
            $table->decimal('kwiii', 10,3)->nullable();
            $table->decimal('kvaiii', 10,3)->nullable();
            $table->decimal('kvariii',10,3)->nullable();
            $table->decimal('kwhiii', 12,3)->nullable();
            $table->decimal('cost', 12,2)->nullable();
            $table->timestamp('time')->nullable();
            $table->timestamp('time_end')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('readings');
    }
};
