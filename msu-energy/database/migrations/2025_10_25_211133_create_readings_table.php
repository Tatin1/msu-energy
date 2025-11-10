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
            $table->decimal('voltage1',8,2)->nullable();
            $table->decimal('voltage2',8,2)->nullable();
            $table->decimal('voltage3',8,2)->nullable();
            $table->decimal('current1',8,2)->nullable();
            $table->decimal('current2',8,2)->nullable();
            $table->decimal('current3',8,2)->nullable();
            $table->decimal('power_factor', 5,3)->nullable();
            $table->decimal('active_power', 10,3)->nullable(); // kW
            $table->decimal('reactive_power',10,3)->nullable(); // kVAr
            $table->decimal('apparent_power',10,3)->nullable(); // kVA
            $table->decimal('kwh', 12,3)->nullable();
            $table->timestamp('recorded_at')->nullable();
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
