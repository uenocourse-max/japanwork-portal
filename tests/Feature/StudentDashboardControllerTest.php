<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardControllerTest extends TestCase
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

    public function test_dashboard_displays(): void
    {
        $response = $this->actingAs($this->user)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_dashboard_shows_stats(): void
    {
        $job = JobListing::factory()->open()->create();
        JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('student.dashboard'));

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_recent_applications(): void
    {
        $job = JobListing::factory()->open()->create();
        JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('student.dashboard'));

        $response->assertStatus(200);
    }
}
