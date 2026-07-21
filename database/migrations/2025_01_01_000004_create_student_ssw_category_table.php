<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_ssw_category', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ssw_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['student_id', 'ssw_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_ssw_category');
    }
};
