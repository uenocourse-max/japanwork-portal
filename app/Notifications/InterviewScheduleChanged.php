<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewScheduleChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array{interview_type: string|null, interview_date: string|null, interview_location: string|null, interview_notes: string|null}  $oldSchedule
     * @param  array{interview_type: string|null, interview_date: string|null, interview_location: string|null, interview_notes: string|null}  $newSchedule
     */
    public function __construct(
        public JobApplication $application,
        public array $oldSchedule,
        public array $newSchedule,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $oldType = $this->oldSchedule['interview_type'] === 'online' ? 'Online (Meeting)' : 'Langsung';
        $newType = $this->newSchedule['interview_type'] === 'online' ? 'Online (Meeting)' : 'Langsung';

        return [
            'type' => 'interview_schedule_changed',
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobListing->title ?? '-',
            'company_name' => $this->application->jobListing->company_name ?? '-',
            'old_type' => $oldType,
            'old_date' => $this->oldSchedule['interview_date'],
            'old_location' => $this->oldSchedule['interview_location'],
            'old_notes' => $this->oldSchedule['interview_notes'],
            'new_type' => $newType,
            'new_date' => $this->newSchedule['interview_date'],
            'new_location' => $this->newSchedule['interview_location'],
            'new_notes' => $this->newSchedule['interview_notes'],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $oldType = $this->oldSchedule['interview_type'] === 'online' ? 'Online (Meeting)' : 'Langsung';
        $newType = $this->newSchedule['interview_type'] === 'online' ? 'Online (Meeting)' : 'Langsung';

        $mail = (new MailMessage)
            ->subject('Jadwal Interview Diubah')
            ->line("Jadwal interview Anda untuk **{$this->application->jobListing->title}** di **{$this->application->jobListing->company_name}** telah diubah.")
            ->line('**Jadwal Sebelumnya:**')
            ->line("Jenis: {$oldType}")
            ->line("Tanggal: {$this->oldSchedule['interview_date']}")
            ->line("Lokasi: {$this->oldSchedule['interview_location']}")
            ->line('**Jadwal Baru:**')
            ->line("Jenis: {$newType}")
            ->line("Tanggal: {$this->newSchedule['interview_date']}")
            ->line("Lokasi: {$this->newSchedule['interview_location']}");

        if ($this->newSchedule['interview_notes']) {
            $mail->line("Catatan: {$this->newSchedule['interview_notes']}");
        }

        return $mail
            ->action('Lihat Lamaran', url('/student/applications'))
            ->line('Terima kasih telah menggunakan Japan Work Program.');
    }
}
