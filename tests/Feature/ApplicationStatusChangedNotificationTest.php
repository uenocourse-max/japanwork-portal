<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApplicationStatusChangedNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Student $student;

    protected JobApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        SswCategory::factory()->create(['name' => 'Perawatan']);

        $this->user = User::factory()->create(['role' => 'student']);
        $this->student = Student::factory()->create(['user_id' => $this->user->id]);

        $job = JobListing::factory()->open()->create([
            'title' => 'Perawat Lansia',
            'company_name' => 'PT Sakura Care',
        ]);
        $this->application = JobApplication::factory()->reviewed()->create([
            'job_listing_id' => $job->id,
            'student_id' => $this->student->id,
            'notes' => 'Silakan ikuti interview',
        ]);
    }

    public function test_notification_uses_database_and_mail_channels(): void
    {
        $notification = new ApplicationStatusChanged($this->application, ApplicationStatus::Reviewed->value);

        $this->assertSame(['database', 'mail'], $notification->via($this->user));
    }

    public function test_to_array_contains_status_change_payload(): void
    {
        $notification = new ApplicationStatusChanged($this->application, ApplicationStatus::Reviewed->value);

        $data = $notification->toArray($this->user);

        $this->assertSame('application_status_changed', $data['type']);
        $this->assertSame($this->application->id, $data['application_id']);
        $this->assertSame('Perawat Lansia', $data['job_title']);
        $this->assertSame('PT Sakura Care', $data['company_name']);
        $this->assertSame(ApplicationStatus::Reviewed->value, $data['old_status']);
        $this->assertSame(ApplicationStatus::Reviewed->value, $data['new_status']);
        $this->assertSame('Direview', $data['status_label']);
        $this->assertSame('Silakan ikuti interview', $data['notes']);
    }

    public function test_to_mail_contains_updated_status_line(): void
    {
        $notification = new ApplicationStatusChanged($this->application, ApplicationStatus::Reviewed->value);

        $mail = $notification->toMail($this->user);

        $this->assertSame('Status Lamaran Diperbarui', $mail->subject);
        $this->assertStringContainsString('Perawat Lansia', $mail->render());
        $this->assertStringContainsString('PT Sakura Care', $mail->render());
        $this->assertStringContainsString('Direview', $mail->render());
        $this->assertStringContainsString('Silakan ikuti interview', $mail->render());
    }

    public function test_to_mail_includes_interview_details_when_scheduled(): void
    {
        $interviewApplication = JobApplication::factory()->interviewScheduled()->create([
            'job_listing_id' => $this->application->job_listing_id,
            'student_id' => $this->student->id,
            'interview_type' => 'online',
            'interview_date' => now()->addDays(3),
            'interview_location' => 'https://meet.google.com/abc-defg-hij',
            'interview_notes' => 'Siapkan KTP',
        ]);

        $notification = new ApplicationStatusChanged($interviewApplication, ApplicationStatus::Accepted->value);

        $mail = $notification->toMail($this->user);
        $rendered = $mail->render();

        $this->assertStringContainsString('Online (Meeting)', $rendered);
        $this->assertStringContainsString('https://meet.google.com/abc-defg-hij', $rendered);
        $this->assertStringContainsString('Siapkan KTP', $rendered);
    }

    public function test_notification_is_stored_in_database_when_sent(): void
    {
        Notification::fake();

        $this->user->notify(new ApplicationStatusChanged($this->application, ApplicationStatus::Reviewed->value));

        Notification::assertSentTo(
            $this->user,
            ApplicationStatusChanged::class,
            fn (ApplicationStatusChanged $notification): bool => $notification->application->is($this->application)
                && $notification->oldStatus === ApplicationStatus::Reviewed->value,
        );
    }
}
