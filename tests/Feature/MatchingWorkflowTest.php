<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\MatchingStatus;
use App\Filament\Recruiter\Resources\RecruiterApplications\Pages\ListRecruiterApplications;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MatchingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $recruiter;

    protected Student $student;

    protected JobListing $job;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('recruiter');

        SswCategory::factory()->create(['name' => 'Perawatan']);

        $this->recruiter = User::factory()->create(['role' => 'recruiter']);
        $studentUser = User::factory()->create(['role' => 'student']);
        $this->student = Student::factory()->create(['user_id' => $studentUser->id]);

        $this->job = JobListing::factory()->open()->create([
            'company_name' => 'PT Jepang Sejahtera',
            'posted_by' => $this->recruiter->id,
        ]);
    }

    public function test_company_accept_sets_student_matching_to_matched(): void
    {
        $application = JobApplication::factory()->interviewScheduled()->create([
            'job_listing_id' => $this->job->id,
            'student_id' => $this->student->id,
        ]);

        Livewire::actingAs($this->recruiter)
            ->test(ListRecruiterApplications::class)
            ->callAction(TestAction::make('input_result')->table($application), [
                'result' => ApplicationStatus::CompanyAccepted->value,
                'notes' => 'Lulus interview di kantor Osaka',
            ]);

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::CompanyAccepted->value,
            'notes' => 'Lulus interview di kantor Osaka',
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $this->student->id,
            'matching_status' => MatchingStatus::Matched->value,
            'matched_company_name' => 'PT Jepang Sejahtera',
        ]);
    }

    public function test_company_accept_notifies_student_user(): void
    {
        $application = JobApplication::factory()->interviewScheduled()->create([
            'job_listing_id' => $this->job->id,
            'student_id' => $this->student->id,
        ]);

        Livewire::actingAs($this->recruiter)
            ->test(ListRecruiterApplications::class)
            ->callAction(TestAction::make('input_result')->table($application), [
                'result' => ApplicationStatus::CompanyAccepted->value,
            ]);

        $notification = $this->student->user->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('App\Notifications\ApplicationStatusChanged', $notification->type);

        $data = $notification->data;
        $this->assertSame('application_status_changed', $data['type']);
        $this->assertSame(ApplicationStatus::InterviewScheduled->value, $data['old_status']);
        $this->assertSame(ApplicationStatus::CompanyAccepted->value, $data['new_status']);
        $this->assertSame('PT Jepang Sejahtera', $data['company_name']);
    }

    public function test_not_passed_result_does_not_change_matching_status(): void
    {
        $application = JobApplication::factory()->interviewScheduled()->create([
            'job_listing_id' => $this->job->id,
            'student_id' => $this->student->id,
        ]);

        Livewire::actingAs($this->recruiter)
            ->test(ListRecruiterApplications::class)
            ->callAction(TestAction::make('input_result')->table($application), [
                'result' => ApplicationStatus::NotPassed->value,
            ]);

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => ApplicationStatus::NotPassed->value,
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $this->student->id,
            'matching_status' => MatchingStatus::NotMatched->value,
            'matched_company_name' => null,
        ]);
    }

    public function test_notification_records_old_status_before_update(): void
    {
        $application = JobApplication::factory()->interviewScheduled()->create([
            'job_listing_id' => $this->job->id,
            'student_id' => $this->student->id,
        ]);

        Livewire::actingAs($this->recruiter)
            ->test(ListRecruiterApplications::class)
            ->callAction(TestAction::make('input_result')->table($application), [
                'result' => ApplicationStatus::CompanyAccepted->value,
            ]);

        $data = $this->student->user->notifications()->first()->data;

        $this->assertSame(ApplicationStatus::InterviewScheduled->value, $data['old_status']);
        $this->assertSame($this->job->title, $data['job_title']);
        $this->assertSame($application->id, $data['application_id']);
        $this->assertNotNull($data['status_label']);
    }
}
