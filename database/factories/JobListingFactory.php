<?php

namespace Database\Factories;

use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobListing>
 */
class JobListingFactory extends Factory
{
    protected $model = JobListing::class;

    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(3),
            'requirements' => fake()->paragraph(2),
            'salary_min' => fake()->numberBetween(150000, 200000),
            'salary_max' => fake()->numberBetween(250000, 350000),
            'location' => fake()->randomElement(config('prefectures.all')),
            'company_name' => fake()->company(),
            'company_description' => fake()->paragraph(2),
            'ssw_category_id' => SswCategory::inRandomOrder()->first(),
            'job_type' => fake()->randomElement(['magang', 'tg', 'engineer']),
            'jlpt_level_required' => fake()->randomElement(['N5', 'N4', 'N3', 'N2', 'N1', null]),
            'participant_status_required' => fake()->randomElement(['ex', 'new_comer', 'any']),
            'status' => 'open',
            'posted_by' => User::factory(),
            'deadline' => fake()->dateTimeBetween('+1 month', '+6 months'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function open(): static
    {
        return $this->state(fn () => ['status' => 'open']);
    }

    public function closed(): static
    {
        return $this->state(fn () => ['status' => 'closed']);
    }

    public function filled(): static
    {
        return $this->state(fn () => ['status' => 'filled']);
    }
}
