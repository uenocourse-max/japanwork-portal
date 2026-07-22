<?php

namespace App\Filament\Recruiter\Widgets;

use App\Models\JobApplication;
use App\Models\JobListing;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecruiterStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $userId = (int) auth()->id();

        $stats = JobListing::where('posted_by', $userId)
            ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status = 'open' THEN 1 END) as open_jobs")
            ->first();

        $applicationStats = JobApplication::whereHas('jobListing', fn ($q) => $q->where('posted_by', $userId))
            ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending, COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted, COUNT(CASE WHEN status = 'interview_scheduled' THEN 1 END) as interview_scheduled, COUNT(CASE WHEN status = 'company_accepted' THEN 1 END) as company_accepted")
            ->first();

        return [
            Stat::make('Lowongan Saya', $stats->total)
                ->description('Jumlah lowongan yang diposting')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),
            Stat::make('Lowongan Aktif', $stats->open_jobs)
                ->description('Lowongan yang sedang dibuka')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Total Lamaran', $applicationStats->total)
                ->description('Lamaran yang masuk')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),
            Stat::make('Menunggu Review', $applicationStats->pending)
                ->description('Lamaran belum direview')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Diterima', $applicationStats->accepted)
                ->description('Lamaran yang diterima')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Jadwal Interview', $applicationStats->interview_scheduled)
                ->description('Menunggu hasil interview')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('Diterima Perusahaan', $applicationStats->company_accepted)
                ->description('Lolos seleksi perusahaan')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
        ];
    }
}
