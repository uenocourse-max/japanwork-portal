<?php

namespace Tests\Feature;

use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureStudentProfileCompleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        SswCategory::factory()->create(['name' => 'Perawatan']);

        $this->user = User::factory()->create(['role' => 'student']);
    }

    public function test_incomplete_profile_redirects_to_edit(): void
    {
        $response = $this->actingAs($this->user)->get(route('student.dashboard'));

        $response->assertRedirect(route('student.profile.edit'));
    }

    public function test_complete_profile_allows_access(): void
    {
        Student::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('student.dashboard'));

        $response->assertStatus(200);
    }

    public function test_profile_edit_not_redirected(): void
    {
        $response = $this->actingAs($this->user)->get(route('student.profile.edit'));

        $response->assertStatus(200);
    }

    public function test_profile_update_not_redirected(): void
    {
        $cat = SswCategory::first();

        $response = $this->actingAs($this->user)->put(route('student.profile.update'), [
            'full_name' => 'Test',
            'age' => 25,
            'birth_place' => 'Jakarta',
            'birth_date' => '2000-01-01',
            'address' => 'Jl. Test',
            'height_cm' => 170,
            'weight_kg' => 65,
            'blood_type' => 'A',
            'gender' => 'male',
            'marital_status' => 'single',
            'phone_number' => '081234567890',
            'participant_status' => 'ex',
            'japanese_learning_months' => 12,
            'pathway' => 'mandiri',
            'matching_status' => 'not_matched',
            'ssw_categories' => [$cat->id],
        ]);

        $response->assertRedirect(route('student.profile'));
    }

    public function test_admin_user_not_affected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('student.dashboard'));

        $response->assertStatus(403);
    }
}
