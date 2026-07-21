<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
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

    public function test_index_shows_applications(): void
    {
        $job = JobListing::factory()->open()->create();
        JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('student.applications.index'));

        $response->assertStatus(200);
        $response->assertSee($job->title);
    }

    public function test_index_does_not_show_other_student_applications(): void
    {
        $other = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::factory()->create(['user_id' => $other->id]);

        $job = JobListing::factory()->open()->create();
        JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $otherStudent->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('student.applications.index'));

        $response->assertStatus(200);
        $response->assertDontSee($job->title);
    }

    public function test_can_withdraw_pending_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'withdrawn',
        ]);
    }

    public function test_cannot_withdraw_reviewed_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->reviewed()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('job_applications', [
            'id' => $application->id,
            'status' => 'withdrawn',
        ]);
    }

    public function test_cannot_withdraw_accepted_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->accepted()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('error');
    }

    public function test_cannot_withdraw_other_student_application(): void
    {
        $other = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::factory()->create(['user_id' => $other->id]);

        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $otherStudent->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertStatus(403);
    }

    public function test_notifications_page(): void
    {
        $this->user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\ApplicationStatusChanged',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('student.notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi');
    }

    public function test_mark_all_notifications_read(): void
    {
        $this->user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\ApplicationStatusChanged',
            'data' => ['message' => 'Test'],
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.notifications.markAllRead'));

        $response->assertStatus(302);
        $this->assertNotNull($this->user->notifications()->first()->read_at);
    }
}
