<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'job_listing_id' => JobListing::factory(),
            'student_id' => Student::factory(),
            'status' => 'pending',
            'applied_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function reviewed(): static
    {
        return $this->state(fn () => [
            'status' => 'reviewed',
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => 'accepted',
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function interviewScheduled(): static
    {
        return $this->state(fn () => [
            'status' => 'interview_scheduled',
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'interview_type' => fake()->randomElement(['online', 'offline']),
            'interview_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'interview_location' => fake()->randomElement(['https://meet.google.com/abc-defg-hij', 'Kantor Jakarta', 'Kantor Osaka']),
            'interview_notes' => fake()->optional()->sentence(),
        ]);
    }

    public function companyAccepted(): static
    {
        return $this->state(fn () => [
            'status' => 'company_accepted',
            'reviewed_at' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
            'interview_type' => fake()->randomElement(['online', 'offline']),
            'interview_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'interview_location' => fake()->randomElement(['https://meet.google.com/abc-defg-hij', 'Kantor Jakarta']),
            'interview_notes' => fake()->optional()->sentence(),
        ]);
    }

    public function notPassed(): static
    {
        return $this->state(fn () => [
            'status' => 'not_passed',
            'reviewed_at' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
            'interview_type' => fake()->randomElement(['online', 'offline']),
            'interview_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'interview_location' => fake()->randomElement(['https://meet.google.com/abc-defg-hij', 'Kantor Jakarta']),
            'interview_notes' => fake()->optional()->sentence(),
        ]);
    }
}
