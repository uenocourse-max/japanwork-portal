<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobControllerTest extends TestCase
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

    public function test_index_shows_only_open_and_available_jobs(): void
    {
        JobListing::factory()->open()->create(['title' => 'Open Job']);
        JobListing::factory()->closed()->create(['title' => 'Closed Job']);
        JobListing::factory()->filled()->create(['title' => 'Filled Job']);

        $response = $this->actingAs($this->user)->get(route('student.jobs.index'));

        $response->assertStatus(200);
        $response->assertSee('Open Job');
        $response->assertDontSee('Closed Job');
        $response->assertDontSee('Filled Job');
    }

    public function test_index_hides_jobs_past_deadline(): void
    {
        JobListing::factory()->open()->create([
            'title' => 'Expired Job',
            'deadline' => now()->subDay(),
        ]);
        $job = JobListing::factory()->open()->create([
            'title' => 'Active Job',
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->actingAs($this->user)->get(route('student.jobs.index'));

        $response->assertDontSee('Expired Job');
        $response->assertSee('Active Job');
    }

    public function test_show_returns_404_for_expired_job(): void
    {
        $job = JobListing::factory()->open()->create([
            'deadline' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)->get(route('student.jobs.show', $job));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_closed_job(): void
    {
        $job = JobListing::factory()->closed()->create();

        $response = $this->actingAs($this->user)->get(route('student.jobs.show', $job));

        $response->assertNotFound();
    }

    public function test_show_displays_open_job(): void
    {
        $job = JobListing::factory()->open()->create([
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->actingAs($this->user)->get(route('student.jobs.show', $job));

        $response->assertStatus(200);
        $response->assertSee($job->title);
    }

    public function test_cannot_apply_to_expired_job(): void
    {
        $job = JobListing::factory()->open()->create([
            'deadline' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)->post(route('student.jobs.apply', $job));

        $response->assertSessionHas('error', 'Lowongan ini sudah tidak tersedia.');
    }

    public function test_cannot_apply_twice(): void
    {
        $job = JobListing::factory()->open()->create([
            'deadline' => now()->addMonth(),
        ]);

        $this->actingAs($this->user)->post(route('student.jobs.apply', $job));
        $response = $this->actingAs($this->user)->post(route('student.jobs.apply', $job));

        $response->assertSessionHas('error', 'Anda sudah melamar untuk lowongan ini.');
    }

    public function test_apply_creates_application(): void
    {
        $job = JobListing::factory()->open()->create([
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->actingAs($this->user)->post(route('student.jobs.apply', $job));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('job_applications', [
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_apply_redirects_if_no_student_record(): void
    {
        $this->student->delete();

        $job = JobListing::factory()->open()->create([
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->actingAs($this->user)->post(route('student.jobs.apply', $job));

        $response->assertRedirect(route('student.profile.edit'));
    }
}
