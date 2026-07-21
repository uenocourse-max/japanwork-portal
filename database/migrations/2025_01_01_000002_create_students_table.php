<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->integer('age');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->text('address');
            $table->integer('height_cm');
            $table->integer('weight_kg');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O']);
            $table->enum('marital_status', ['single', 'married']);
            $table->string('phone_number')->unique();
            $table->enum('participant_status', ['ex', 'new_comer']);
            $table->integer('jft_score')->nullable();
            $table->enum('jlpt_level', ['N5', 'N4', 'N3', 'N2', 'N1'])->nullable();
            $table->integer('japanese_learning_months');
            $table->enum('pathway', ['mandiri', 'lpk']);
            $table->string('lpk_name')->nullable();
            $table->text('photo_drive_url')->nullable();
            $table->text('cv_drive_url')->nullable();
            $table->enum('matching_status', ['process_matching', 'matched', 'waiting_result', 'not_matched'])->default('not_matched');
            $table->string('matched_company_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
