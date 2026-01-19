<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('system_logs', 'building')) {
            Schema::table('system_logs', function (Blueprint $table) {
                $table->string('building')->default('SYSTEM')->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('system_logs', 'building')) {
            Schema::table('system_logs', function (Blueprint $table) {
                $table->dropColumn('building');
            });
        }
    }
};
