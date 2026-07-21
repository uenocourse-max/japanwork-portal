<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_applications DROP CONSTRAINT IF EXISTS job_applications_status_check');
            DB::statement("ALTER TABLE job_applications ADD CONSTRAINT job_applications_status_check CHECK (status IN ('pending', 'reviewed', 'interview_scheduled', 'waiting_interview_result', 'accepted', 'rejected'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending', 'reviewed', 'interview_scheduled', 'waiting_interview_result', 'accepted', 'rejected') DEFAULT 'pending'");
        }
        // SQLite has no strict ENUM — values are enforced by application logic
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_applications DROP CONSTRAINT IF EXISTS job_applications_status_check');
            DB::statement("ALTER TABLE job_applications ADD CONSTRAINT job_applications_status_check CHECK (status IN ('pending', 'reviewed', 'accepted', 'rejected'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending'");
        }
    }
};
