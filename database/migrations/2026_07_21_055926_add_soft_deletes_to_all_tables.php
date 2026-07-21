<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('students', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('lpk_tsks', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('job_listings', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('job_applications', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('ssw_categories', fn (Blueprint $table) => $table->softDeletes());
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('students', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('lpk_tsks', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('job_listings', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('job_applications', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('ssw_categories', fn (Blueprint $table) => $table->dropSoftDeletes());
    }
};
