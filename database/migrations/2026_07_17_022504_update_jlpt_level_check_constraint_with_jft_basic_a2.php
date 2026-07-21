<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE students DROP CONSTRAINT students_jlpt_level_check');
            DB::statement("ALTER TABLE students ADD CONSTRAINT students_jlpt_level_check CHECK (jlpt_level IN ('N5', 'N4', 'N3', 'N2', 'N1', 'JFT Basic A2'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE students DROP CONSTRAINT students_jlpt_level_check');
            DB::statement("ALTER TABLE students ADD CONSTRAINT students_jlpt_level_check CHECK (jlpt_level IN ('N5', 'N4', 'N3', 'N2', 'N1'))");
        }
    }
};
