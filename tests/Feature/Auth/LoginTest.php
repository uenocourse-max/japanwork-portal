<?php

namespace Tests\Feature\Auth;

use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SswCategory::factory()->create(['name' => 'Perawatan']);
    }

    public function test_login_form_is_displayed(): void
    {
        $response = $this->get(route('student.login'));

        $response->assertStatus(200);
        $response->assertSee('Login Siswa');
    }

    public function test_student_can_login(): void
    {
        $user = User::factory()->create(['role' => 'student', 'password' => bcrypt('password')]);
        Student::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('student.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_student_with_incomplete_profile_redirects_to_edit(): void
    {
        $user = User::factory()->create(['role' => 'student', 'password' => bcrypt('password')]);

        $response = $this->post(route('student.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.profile.edit'));
    }

    public function test_admin_cannot_login_via_student_login(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        $response = $this->post(route('student.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_recruiter_cannot_login_via_student_login(): void
    {
        $user = User::factory()->create(['role' => 'recruiter', 'password' => bcrypt('password')]);

        $response = $this->post(route('student.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_wrong_password_shows_error(): void
    {
        $user = User::factory()->create(['role' => 'student', 'password' => bcrypt('password')]);

        $response = $this->post(route('student.login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_student_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        Student::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('student.logout'));

        $response->assertRedirect(route('student.login'));
        $this->assertGuest();
    }

    public function test_logged_in_student_redirected_from_login_form(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        Student::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('student.login'));

        $response->assertRedirect(route('student.dashboard'));
    }
}
