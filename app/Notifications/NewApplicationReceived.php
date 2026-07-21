<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_application_received',
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobListing->title ?? '-',
            'student_name' => $this->application->student->full_name ?? '-',
            'jlpt_level' => $this->application->student->jlpt_level ?? '-',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->application->student->full_name ?? '-';
        $jobTitle = $this->application->jobListing->title ?? '-';
        $jlpt = $this->application->student->jlpt_level ?? '-';

        return (new MailMessage)
            ->subject('Lamaran Baru Diterima')
            ->line("**{$studentName}** telah melamar ke lowongan **{$jobTitle}**.")
            ->line("JLPT: {$jlpt}")
            ->action('Lihat Lamaran', url('/recruiter/recruiter-applications'))
            ->line('Terima kasih.');
    }
}
