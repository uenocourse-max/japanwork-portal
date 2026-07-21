<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_applications DROP CONSTRAINT IF EXISTS job_applications_status_check');
            DB::statement("ALTER TABLE job_applications ADD CONSTRAINT job_applications_status_check CHECK (status IN ('pending', 'reviewed', 'accepted', 'interview_scheduled', 'company_accepted', 'not_passed', 'rejected', 'withdrawn'))");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            Schema::create('job_applications_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->string('status', 30)->default('pending');
                $table->text('notes')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('interview_type')->nullable();
                $table->dateTime('interview_date')->nullable();
                $table->string('interview_location')->nullable();
                $table->text('interview_notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index('status');
            });

            DB::statement('INSERT INTO job_applications_new SELECT * FROM job_applications');
            Schema::drop('job_applications');
            Schema::rename('job_applications_new', 'job_applications');

            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_applications DROP CONSTRAINT IF EXISTS job_applications_status_check');
            DB::statement("ALTER TABLE job_applications ADD CONSTRAINT job_applications_status_check CHECK (status IN ('pending', 'reviewed', 'accepted', 'interview_scheduled', 'company_accepted', 'not_passed', 'rejected'))");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            Schema::create('job_applications_old', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->enum('status', ['pending', 'reviewed', 'accepted', 'rejected'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('interview_type')->nullable();
                $table->dateTime('interview_date')->nullable();
                $table->string('interview_location')->nullable();
                $table->text('interview_notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index('status');
            });

            DB::statement('INSERT INTO job_applications_old SELECT * FROM job_applications');
            Schema::drop('job_applications');
            Schema::rename('job_applications_old', 'job_applications');

            DB::statement('PRAGMA foreign_keys = ON');
        }
    }
};
