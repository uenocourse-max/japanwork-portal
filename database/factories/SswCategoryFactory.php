<?php

namespace Database\Factories;

use App\Models\SswCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SswCategoryFactory extends Factory
{
    protected $model = SswCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Perawatan', 'Konstruksi', 'Makanan & Minuman', 'Perhotelan', 'Pertanian']),
        ];
    }
}
