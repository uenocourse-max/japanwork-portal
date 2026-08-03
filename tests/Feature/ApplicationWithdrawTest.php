<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\MatchingStatus;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationWithdrawTest extends TestCase
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

    public function test_cannot_withdraw_an_already_withdrawn_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
            'status' => ApplicationStatus::Withdrawn->value,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::Withdrawn->value,
        ]);
    }

    public function test_cannot_withdraw_rejected_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->rejected()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::Rejected->value,
        ]);
    }

    public function test_cannot_withdraw_not_passed_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->notPassed()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::NotPassed->value,
        ]);
    }

    public function test_cannot_withdraw_interview_scheduled_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->interviewScheduled()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::InterviewScheduled->value,
        ]);
    }

    public function test_cannot_withdraw_company_accepted_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->companyAccepted()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::CompanyAccepted->value,
        ]);
    }

    public function test_guest_cannot_withdraw_application(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->post(route('student.applications.withdraw', $application));

        $response->assertRedirect(route('student.login'));
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::Pending->value,
        ]);
    }

    public function test_withdraw_does_not_alter_student_matching_status(): void
    {
        $this->student->update(['matching_status' => MatchingStatus::ProcessMatching->value]);

        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('student.applications.withdraw', $application));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('students', [
            'id' => $this->student->id,
            'matching_status' => MatchingStatus::ProcessMatching->value,
        ]);
    }

    public function test_withdraw_is_not_allowed_via_get_request(): void
    {
        $job = JobListing::factory()->open()->create();
        $application = JobApplication::factory()->pending()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('student.applications.withdraw', $application));

        $response->assertMethodNotAllowed();
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::Pending->value,
        ]);
    }
}
