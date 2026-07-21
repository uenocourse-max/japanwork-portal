<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('job_listings')
            ->where('location', 'Nagoya')
            ->update(['location' => 'Aichi']);

        DB::table('job_listings')
            ->where('location', 'Yokohama')
            ->update(['location' => 'Kanagawa']);

        DB::table('job_listings')
            ->where('location', 'Sendai')
            ->update(['location' => 'Miyagi']);

        DB::table('job_listings')
            ->where('location', 'Sapporo')
            ->update(['location' => 'Hokkaido']);

        DB::table('job_listings')
            ->where('location', 'Kobe')
            ->update(['location' => 'Hyogo']);

        DB::table('job_listings')
            ->where('location', 'Hamamatsu')
            ->update(['location' => 'Shizuoka']);

        DB::table('job_listings')
            ->where('location', '3')
            ->update(['location' => 'Ibaraki']);
    }

    public function down(): void {}
};
