<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('role');
            $table->string('company_name')->nullable()->after('phone_number');
            $table->string('location')->nullable()->after('company_name');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'student', 'recruiter'))");
        } elseif (DB::getDriverName() === 'sqlite') {
            DB::statement('CREATE TEMPORARY TABLE users_backup AS SELECT * FROM users');
            DB::statement('DROP TABLE users');
            DB::statement("CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR NOT NULL, role VARCHAR NOT NULL DEFAULT 'student', phone_number VARCHAR NULL, company_name VARCHAR NULL, location VARCHAR NULL, email VARCHAR UNIQUE NOT NULL, email_verified_at TIMESTAMP NULL, password VARCHAR NOT NULL, remember_token VARCHAR NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)");
            DB::statement('INSERT INTO users SELECT * FROM users_backup');
            DB::statement('DROP TABLE users_backup');
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'company_name', 'location']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'student'))");
        } elseif (DB::getDriverName() === 'sqlite') {
            DB::statement('CREATE TEMPORARY TABLE users_backup AS SELECT * FROM users');
            DB::statement('DROP TABLE users');
            DB::statement("CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR NOT NULL, role VARCHAR NOT NULL DEFAULT 'student', email VARCHAR UNIQUE NOT NULL, email_verified_at TIMESTAMP NULL, password VARCHAR NOT NULL, remember_token VARCHAR NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)");
            DB::statement('INSERT INTO users SELECT * FROM users_backup');
            DB::statement('DROP TABLE users_backup');
        }
    }
};
