<?php

namespace Database\Factories;

use App\Enums\JlptLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
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
            'job_type' => fake()->randomElement(JobType::cases())->value,
            'jlpt_level_required' => fake()->randomElement([...JlptLevel::cases(), null])?->value,
            'participant_status_required' => fake()->randomElement(['ex', 'new_comer', 'any']),
            'status' => JobStatus::Open->value,
            'posted_by' => User::factory(),
            'deadline' => fake()->dateTimeBetween('+1 month', '+6 months'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => JobStatus::Draft->value]);
    }

    public function open(): static
    {
        return $this->state(fn () => ['status' => JobStatus::Open->value]);
    }

    public function closed(): static
    {
        return $this->state(fn () => ['status' => JobStatus::Closed->value]);
    }

    public function filled(): static
    {
        return $this->state(fn () => ['status' => JobStatus::Filled->value]);
    }
}
