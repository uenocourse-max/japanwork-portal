<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $exists = DB::select("SELECT 1 FROM pg_constraint WHERE conname = 'saved_jobs_student_id_job_listing_id_unique'");
            if (empty($exists)) {
                Schema::table('saved_jobs', function ($table) {
                    $table->unique(['student_id', 'job_listing_id']);
                });
            }
        } elseif ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('saved_jobs')");
            $hasUnique = false;
            foreach ($indexes as $index) {
                if ($index->unique) {
                    $hasUnique = true;
                    break;
                }
            }
            if (! $hasUnique) {
                Schema::table('saved_jobs', function ($table) {
                    $table->unique(['student_id', 'job_listing_id']);
                });
            }
        } else {
            Schema::table('saved_jobs', function ($table) {
                $table->unique(['student_id', 'job_listing_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('saved_jobs', function ($table) {
            $table->dropUnique(['student_id', 'job_listing_id']);
        });
    }
};
