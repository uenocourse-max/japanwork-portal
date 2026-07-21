<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->string('location');
            $table->string('company_name');
            $table->text('company_description')->nullable();
            $table->foreignId('ssw_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('jlpt_level_required')->nullable();
            $table->enum('participant_status_required', ['ex', 'new_comer', 'any'])->default('any');
            $table->enum('status', ['draft', 'open', 'closed', 'filled'])->default('draft');
            $table->foreignId('posted_by')->constrained('users');
            $table->date('deadline')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('company_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
