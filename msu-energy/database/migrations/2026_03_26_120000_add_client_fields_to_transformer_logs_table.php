<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transformer_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('transformer_logs', 'meter_id')) {
                $table->foreignId('meter_id')->nullable()->constrained()->nullOnDelete()->after('id');
            }

            if (!Schema::hasColumn('transformer_logs', 'date')) {
                $table->date('date')->nullable()->after('meter_id');
            }

            if (!Schema::hasColumn('transformer_logs', 'time')) {
                $table->time('time')->nullable()->after('date');
            }

            if (!Schema::hasColumn('transformer_logs', 'time_ed')) {
                $table->time('time_ed')->nullable()->after('time');
            }

            if (!Schema::hasColumn('transformer_logs', 'kw1')) {
                $table->decimal('kw1', 10, 3)->nullable()->after('a3');
            }

            if (!Schema::hasColumn('transformer_logs', 'kw2')) {
                $table->decimal('kw2', 10, 3)->nullable()->after('kw1');
            }

            if (!Schema::hasColumn('transformer_logs', 'kw3')) {
                $table->decimal('kw3', 10, 3)->nullable()->after('kw2');
            }

            if (!Schema::hasColumn('transformer_logs', 'pf1')) {
                $table->decimal('pf1', 5, 3)->nullable()->after('kw3');
            }

            if (!Schema::hasColumn('transformer_logs', 'pf2')) {
                $table->decimal('pf2', 5, 3)->nullable()->after('pf1');
            }

            if (!Schema::hasColumn('transformer_logs', 'pf3')) {
                $table->decimal('pf3', 5, 3)->nullable()->after('pf2');
            }

            if (!Schema::hasColumn('transformer_logs', 'kwiii')) {
                $table->decimal('kwiii', 10, 3)->nullable()->after('pf3');
            }

            if (!Schema::hasColumn('transformer_logs', 'kvaiii')) {
                $table->decimal('kvaiii', 10, 3)->nullable()->after('kwiii');
            }

            if (!Schema::hasColumn('transformer_logs', 'kvariii')) {
                $table->decimal('kvariii', 10, 3)->nullable()->after('kvaiii');
            }

            if (!Schema::hasColumn('transformer_logs', 'pfiii')) {
                $table->decimal('pfiii', 5, 3)->nullable()->after('kvariii');
            }

            if (!Schema::hasColumn('transformer_logs', 'cost')) {
                $table->decimal('cost', 10, 2)->nullable()->after('kwh');
            }
        });

        if (Schema::hasColumn('transformer_logs', 'pf') && Schema::hasColumn('transformer_logs', 'pfiii')) {
            DB::table('transformer_logs')
                ->whereNull('pfiii')
                ->whereNotNull('pf')
                ->update(['pfiii' => DB::raw('pf')]);
        }
    }

    public function down(): void
    {
        Schema::table('transformer_logs', function (Blueprint $table) {
            $dropColumns = [
                'meter_id',
                'date',
                'time',
                'time_ed',
                'kw1',
                'kw2',
                'kw3',
                'pf1',
                'pf2',
                'pf3',
                'kwiii',
                'kvaiii',
                'kvariii',
                'pfiii',
                'cost',
            ];

            foreach ($dropColumns as $column) {
                if (Schema::hasColumn('transformer_logs', $column)) {
                    if ($column === 'meter_id') {
                        $table->dropConstrainedForeignId('meter_id');
                    } else {
                        $table->dropColumn($column);
                    }
                }
            }
        });
    }
};
