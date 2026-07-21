<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        $indexes = [
            ['job_listings', 'title'],
            ['job_listings', 'location'],
            ['job_listings', 'posted_by'],
            ['job_listings', 'ssw_category_id'],
            ['students', 'full_name'],
            ['students', 'matching_status'],
            ['students', 'jlpt_level'],
            ['job_applications', 'student_id'],
            ['job_applications', 'status'],
            ['job_applications', 'applied_at'],
            ['lpk_tsks', 'user_id'],
        ];

        foreach ($indexes as [$table, $column]) {
            $indexName = "{$table}_{$column}_index";

            if ($driver === 'sqlite') {
                try {
                    DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$column})");
                } catch (Throwable) {
                    // ignore duplicate on SQLite
                }
            } else {
                DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$column})");
            }
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        $indexes = [
            ['job_listings', 'title'],
            ['job_listings', 'location'],
            ['job_listings', 'posted_by'],
            ['job_listings', 'ssw_category_id'],
            ['students', 'full_name'],
            ['students', 'matching_status'],
            ['students', 'jlpt_level'],
            ['job_applications', 'student_id'],
            ['job_applications', 'status'],
            ['job_applications', 'applied_at'],
            ['lpk_tsks', 'user_id'],
        ];

        foreach ($indexes as [$table, $column]) {
            $indexName = "{$table}_{$column}_index";

            if ($driver === 'sqlite') {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
            } else {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
            }
        }
    }
};
