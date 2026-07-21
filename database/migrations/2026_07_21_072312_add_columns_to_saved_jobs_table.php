<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_jobs', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $table->unique(['student_id', 'job_listing_id']);
        });
    }

    public function down(): void
    {
        Schema::table('saved_jobs', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'job_listing_id']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['job_listing_id']);
            $table->dropColumn(['student_id', 'job_listing_id']);
        });
    }
};
