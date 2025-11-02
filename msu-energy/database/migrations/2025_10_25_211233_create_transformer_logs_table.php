<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    if (!Schema::hasTable('transformer_logs')) {
        Schema::create('transformer_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('recorded_at')->nullable();
            $table->decimal('frequency', 6, 2)->nullable();
            $table->decimal('v1', 8, 2)->nullable();
            $table->decimal('v2', 8, 2)->nullable();
            $table->decimal('v3', 8, 2)->nullable();
            $table->decimal('a1', 8, 2)->nullable();
            $table->decimal('a2', 8, 2)->nullable();
            $table->decimal('a3', 8, 2)->nullable();
            $table->decimal('pf', 5, 3)->nullable();
            $table->decimal('kwh', 10, 3)->nullable();
            $table->timestamps();
        });
    }
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transformer_logs');
    }
};
