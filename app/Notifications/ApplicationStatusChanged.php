<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
        public string $oldStatus,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = match ($this->application->status) {
            'pending' => 'Menunggu',
            'reviewed' => 'Sudah Direview',
            'accepted' => 'Diterima',
            'interview_scheduled' => 'Jadwal Interview Ditentukan',
            'company_accepted' => 'Diterima Perusahaan',
            'not_passed' => 'Tidak Lolos',
            'rejected' => 'Ditolak',
            default => ucfirst($this->application->status),
        };

        return [
            'type' => 'application_status_changed',
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobListing->title ?? '-',
            'company_name' => $this->application->jobListing->company_name ?? '-',
            'old_status' => $this->oldStatus,
            'new_status' => $this->application->status,
            'status_label' => $statusLabel,
            'notes' => $this->application->notes,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = match ($this->application->status) {
            'pending' => 'Menunggu',
            'reviewed' => 'Sudah Direview',
            'accepted' => 'Diterima',
            'interview_scheduled' => 'Jadwal Interview Ditentukan',
            'company_accepted' => 'Diterima Perusahaan',
            'not_passed' => 'Tidak Lolos',
            'rejected' => 'Ditolak',
            default => ucfirst($this->application->status),
        };

        return (new MailMessage)
            ->subject('Status Lamaran Diperbarui')
            ->line("Lamaran Anda untuk **{$this->application->jobListing->title}** di **{$this->application->jobListing->company_name}** telah diperbarui.")
            ->line("Status terbaru: **{$statusLabel}**")
            ->when($this->application->status === 'interview_scheduled', function ($m) {
                $app = $this->application;
                $type = $app->interview_type === 'online' ? 'Online (Meeting)' : 'Langsung';
                $m->line("Jenis Interview: {$type}");
                if ($app->interview_date) {
                    $m->line("Tanggal: {$app->interview_date->format('d M Y H:i')}");
                }
                if ($app->interview_location) {
                    $m->line("Lokasi/Link: {$app->interview_location}");
                }
                if ($app->interview_notes) {
                    $m->line("Catatan: {$app->interview_notes}");
                }
            })
            ->when($this->application->notes && $this->application->status !== 'interview_scheduled', fn ($m) => $m->line("Catatan: {$this->application->notes}"))
            ->action('Lihat Lamaran', url('/student/applications'))
            ->line('Terima kasih telah menggunakan Japan Work Program.');
    }
}
