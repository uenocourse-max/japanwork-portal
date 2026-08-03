<?php

namespace Database\Factories;

use App\Enums\JlptLevel;
use App\Enums\MatchingStatus;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'age' => fake()->numberBetween(18, 40),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->dateTimeBetween('-30 years', '-18 years'),
            'address' => fake()->address(),
            'height_cm' => fake()->numberBetween(150, 185),
            'weight_kg' => fake()->numberBetween(45, 90),
            'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']),
            'marital_status' => fake()->randomElement(['single', 'married']),
            'phone_number' => fake()->unique()->phoneNumber(),
            'gender' => fake()->randomElement(['male', 'female']),
            'participant_status' => fake()->randomElement(['ex', 'new_comer']),
            'jft_score' => fake()->optional()->numberBetween(0, 480),
            'jlpt_level' => fake()->randomElement([
                JlptLevel::N5->value,
                JlptLevel::N4->value,
                JlptLevel::N3->value,
                JlptLevel::N2->value,
                JlptLevel::N1->value,
            ]),
            'japanese_learning_months' => fake()->numberBetween(0, 36),
            'pathway' => fake()->randomElement(['mandiri', 'lpk']),
            'lpk_name' => null,
            'photo_drive_url' => null,
            'cv_drive_url' => null,
            'matching_status' => MatchingStatus::NotMatched->value,
            'matched_company_name' => null,
        ];
    }
}
