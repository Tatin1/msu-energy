<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time')->nullable();
            $table->time('time_ed')->nullable();
            $table->decimal('total_kw', 10, 2)->nullable();
            $table->decimal('total_kvar', 10, 2)->nullable();
            $table->decimal('total_kva', 10, 2)->nullable();
            $table->decimal('total_pf', 5, 3)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
