<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\SavedJob;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedJobControllerTest extends TestCase
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

    public function test_index_shows_saved_jobs(): void
    {
        $job = JobListing::factory()->open()->create();
        SavedJob::create([
            'student_id' => $this->student->id,
            'job_listing_id' => $job->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('student.bookmarks.index'));

        $response->assertStatus(200);
        $response->assertSee($job->title);
    }

    public function test_index_empty_state(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('student.bookmarks.index'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada bookmark');
    }

    public function test_toggle_saves_job(): void
    {
        $job = JobListing::factory()->open()->create();

        $response = $this->actingAs($this->user)
            ->post(route('student.jobs.bookmark', $job));

        $response->assertSessionHas('success', 'Lowongan disimpan ke bookmark.');
        $this->assertDatabaseHas('saved_jobs', [
            'student_id' => $this->student->id,
            'job_listing_id' => $job->id,
        ]);
    }

    public function test_toggle_removes_saved_job(): void
    {
        $job = JobListing::factory()->open()->create();
        SavedJob::create([
            'student_id' => $this->student->id,
            'job_listing_id' => $job->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.jobs.bookmark', $job));

        $response->assertSessionHas('success', 'Lowongan dihapus dari bookmark.');
        $this->assertSoftDeleted('saved_jobs', [
            'student_id' => $this->student->id,
            'job_listing_id' => $job->id,
        ]);
    }

    public function test_toggle_other_student_job(): void
    {
        $other = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::factory()->create(['user_id' => $other->id]);
        $job = JobListing::factory()->open()->create();
        SavedJob::create([
            'student_id' => $otherStudent->id,
            'job_listing_id' => $job->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.jobs.bookmark', $job));

        $response->assertSessionHas('success', 'Lowongan disimpan ke bookmark.');
        $this->assertDatabaseHas('saved_jobs', [
            'student_id' => $this->student->id,
            'job_listing_id' => $job->id,
            'deleted_at' => null,
        ]);
    }

    public function test_index_does_not_show_other_student_saved_jobs(): void
    {
        $other = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::factory()->create(['user_id' => $other->id]);

        $job = JobListing::factory()->open()->create();
        SavedJob::create([
            'student_id' => $otherStudent->id,
            'job_listing_id' => $job->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('student.bookmarks.index'));

        $response->assertStatus(200);
        $response->assertDontSee($job->title);
    }
}
