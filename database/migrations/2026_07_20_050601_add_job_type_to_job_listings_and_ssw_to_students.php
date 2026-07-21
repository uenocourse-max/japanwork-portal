<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('job_type')->default('tg');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE job_listings ADD CONSTRAINT job_listings_job_type_check CHECK (job_type IN ('magang', 'tg', 'engineer'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE job_listings DROP CONSTRAINT IF EXISTS job_listings_job_type_check');
        }

        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn('job_type');
        });
    }
};
