<?php

namespace Database\Seeders;

use App\Models\LpkTsk;
use App\Models\SswCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $sswCategories = [
            'Kaigo',
            'Food Service',
            'Agriculture',
            'Construction',
            'Manufacturing',
            'Building Cleaning',
            'Hotel',
            'Aviation',
            'Fishery',
        ];

        foreach ($sswCategories as $category) {
            SswCategory::firstOrCreate(['name' => $category]);
        }

        $lpkTskData = [
            ['name' => 'LPK Mitra Sejati', 'email' => 'mitrasejati@example.com', 'location' => 'Jakarta Selatan'],
            ['name' => 'LPK Bintang Samudra', 'email' => 'bintangsamudra@example.com', 'location' => 'Surabaya'],
            ['name' => 'LPK Cemerlang Abadi', 'email' => 'cemerlangabadi@example.com', 'location' => 'Bandung'],
        ];

        foreach ($lpkTskData as $lpk) {
            $user = User::firstOrCreate(
                ['email' => $lpk['email']],
                [
                    'name' => $lpk['name'],
                    'password' => Hash::make('password'),
                    'role' => 'recruiter',
                    'company_name' => $lpk['name'],
                    'location' => $lpk['location'],
                ]
            );

            LpkTsk::firstOrCreate(
                ['email' => $lpk['email']],
                [
                    'user_id' => $user->id,
                    'name' => $lpk['name'],
                    'password' => Hash::make('password'),
                    'location' => $lpk['location'],
                ]
            );
        }
    }
}
