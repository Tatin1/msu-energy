<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('readings', function (Blueprint $table) {
            if (!Schema::hasColumn('readings', 'frequency')) {
                $table->decimal('frequency', 6, 2)->nullable()->after('meter_id');
            }

            if (Schema::hasColumn('readings', 'voltage1')) {
                $table->renameColumn('voltage1', 'v1');
            }
            if (Schema::hasColumn('readings', 'voltage2')) {
                $table->renameColumn('voltage2', 'v2');
            }
            if (Schema::hasColumn('readings', 'voltage3')) {
                $table->renameColumn('voltage3', 'v3');
            }
            if (Schema::hasColumn('readings', 'current1')) {
                $table->renameColumn('current1', 'a1');
            }
            if (Schema::hasColumn('readings', 'current2')) {
                $table->renameColumn('current2', 'a2');
            }
            if (Schema::hasColumn('readings', 'current3')) {
                $table->renameColumn('current3', 'a3');
            }
            if (Schema::hasColumn('readings', 'power_factor')) {
                $table->renameColumn('power_factor', 'pfiii');
            }
            if (Schema::hasColumn('readings', 'active_power')) {
                $table->renameColumn('active_power', 'kwiii');
            }
            if (Schema::hasColumn('readings', 'apparent_power')) {
                $table->renameColumn('apparent_power', 'kvaiii');
            }
            if (Schema::hasColumn('readings', 'reactive_power')) {
                $table->renameColumn('reactive_power', 'kvariii');
            }
            if (Schema::hasColumn('readings', 'kwh')) {
                $table->renameColumn('kwh', 'kwhiii');
            }
        });
    }

    public function down(): void
    {
        Schema::table('readings', function (Blueprint $table) {
            if (Schema::hasColumn('readings', 'frequency')) {
                $table->dropColumn('frequency');
            }
            if (Schema::hasColumn('readings', 'v1')) {
                $table->renameColumn('v1', 'voltage1');
            }
            if (Schema::hasColumn('readings', 'v2')) {
                $table->renameColumn('v2', 'voltage2');
            }
            if (Schema::hasColumn('readings', 'v3')) {
                $table->renameColumn('v3', 'voltage3');
            }
            if (Schema::hasColumn('readings', 'a1')) {
                $table->renameColumn('a1', 'current1');
            }
            if (Schema::hasColumn('readings', 'a2')) {
                $table->renameColumn('a2', 'current2');
            }
            if (Schema::hasColumn('readings', 'a3')) {
                $table->renameColumn('a3', 'current3');
            }
            if (Schema::hasColumn('readings', 'pfiii')) {
                $table->renameColumn('pfiii', 'power_factor');
            }
            if (Schema::hasColumn('readings', 'kwiii')) {
                $table->renameColumn('kwiii', 'active_power');
            }
            if (Schema::hasColumn('readings', 'kvaiii')) {
                $table->renameColumn('kvaiii', 'apparent_power');
            }
            if (Schema::hasColumn('readings', 'kvariii')) {
                $table->renameColumn('kvariii', 'reactive_power');
            }
            if (Schema::hasColumn('readings', 'kwhiii')) {
                $table->renameColumn('kwhiii', 'kwh');
            }
        });
    }
};
