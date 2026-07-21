<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('interview_type')->nullable();
            $table->dateTime('interview_date')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_notes')->nullable();
        });

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_applications DROP CONSTRAINT IF EXISTS job_applications_status_check');
            DB::statement("ALTER TABLE job_applications ADD CONSTRAINT job_applications_status_check CHECK (status IN ('pending', 'reviewed', 'accepted', 'interview_scheduled', 'company_accepted', 'not_passed', 'rejected'))");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_applications DROP CONSTRAINT IF EXISTS job_applications_status_check');
            DB::statement("ALTER TABLE job_applications ADD CONSTRAINT job_applications_status_check CHECK (status IN ('pending', 'reviewed', 'interview_scheduled', 'waiting_interview_result', 'accepted', 'rejected'))");
        }

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['interview_type', 'interview_date', 'interview_location', 'interview_notes']);
        });
    }
};
