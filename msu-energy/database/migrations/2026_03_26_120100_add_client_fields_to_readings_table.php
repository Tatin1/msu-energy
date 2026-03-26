<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('readings', function (Blueprint $table) {
            if (!Schema::hasColumn('readings', 'kw1')) {
                $table->decimal('kw1', 10, 3)->nullable()->after('current3');
            }

            if (!Schema::hasColumn('readings', 'kw2')) {
                $table->decimal('kw2', 10, 3)->nullable()->after('kw1');
            }

            if (!Schema::hasColumn('readings', 'kw3')) {
                $table->decimal('kw3', 10, 3)->nullable()->after('kw2');
            }

            if (!Schema::hasColumn('readings', 'pf1')) {
                $table->decimal('pf1', 5, 3)->nullable()->after('kw3');
            }

            if (!Schema::hasColumn('readings', 'pf2')) {
                $table->decimal('pf2', 5, 3)->nullable()->after('pf1');
            }

            if (!Schema::hasColumn('readings', 'pf3')) {
                $table->decimal('pf3', 5, 3)->nullable()->after('pf2');
            }

            if (!Schema::hasColumn('readings', 'cost')) {
                $table->decimal('cost', 10, 2)->nullable()->after('kwh');
            }
        });
    }

    public function down(): void
    {
        Schema::table('readings', function (Blueprint $table) {
            $dropColumns = ['kw1', 'kw2', 'kw3', 'pf1', 'pf2', 'pf3', 'cost'];

            foreach ($dropColumns as $column) {
                if (Schema::hasColumn('readings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
