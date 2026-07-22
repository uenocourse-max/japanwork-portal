<?php

namespace Tests\Feature;

use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        SswCategory::factory()->create(['name' => 'Perawatan']);

        $this->user = User::factory()->create(['role' => 'student']);
        $this->student = Student::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_profile_show_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('student.profile'));

        $response->assertStatus(200);
    }

    public function test_profile_edit_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('student.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Edit Profil');
    }

    public function test_update_profile(): void
    {
        $cat = SswCategory::first();

        $response = $this->actingAs($this->user)->put(route('student.profile.update'), [
            'full_name' => 'Updated Name',
            'age' => 25,
            'birth_place' => 'Jakarta',
            'birth_date' => '2000-01-01',
            'address' => 'Jl. Test No. 1',
            'height_cm' => 170,
            'weight_kg' => 65,
            'blood_type' => 'A',
            'gender' => 'male',
            'marital_status' => 'single',
            'phone_number' => '081234567890',
            'participant_status' => 'ex',
            'jft_score' => 300,
            'jlpt_level' => 'N3',
            'japanese_learning_months' => 12,
            'pathway' => 'mandiri',
            'lpk_name' => null,
            'matching_status' => 'not_matched',
            'matched_company_name' => null,
            'photo_drive_url' => null,
            'cv_drive_url' => null,
            'ssw_categories' => [$cat->id],
        ]);

        $response->assertRedirect(route('student.profile'));
        $this->assertDatabaseHas('students', [
            'user_id' => $this->user->id,
            'full_name' => 'Updated Name',
        ]);
    }

    public function test_update_profile_validation(): void
    {
        $response = $this->actingAs($this->user)->put(route('student.profile.update'), [
            'full_name' => '',
            'age' => null,
        ]);

        $response->assertSessionHasErrors(['full_name', 'age']);
    }

    public function test_change_password_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('student.password'));

        $response->assertStatus(200);
    }

    public function test_update_password(): void
    {
        $this->user->update(['password' => bcrypt('current-password')]);

        $response = $this->actingAs($this->user)->put(route('student.password.update'), [
            'current_password' => 'current-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHas('success');
    }

    public function test_update_password_wrong_current(): void
    {
        $this->user->update(['password' => bcrypt('current-password')]);

        $response = $this->actingAs($this->user)->put(route('student.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasErrors('current_password');
    }
}
