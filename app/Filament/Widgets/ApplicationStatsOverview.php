<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ApplicationStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $stats = Cache::remember('widget_application_stats', 300, function () {
            return JobApplication::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        });

        return [
            Stat::make('Total Lamaran', array_sum($stats))
                ->description('Jumlah seluruh lamaran')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
            Stat::make('Menunggu', $stats[ApplicationStatus::Pending->value] ?? 0)
                ->description('Lamaran belum direview')
                ->descriptionIcon('heroicon-m-clock')
                ->color(ApplicationStatus::Pending->color()),
            Stat::make('Interview', $stats[ApplicationStatus::InterviewScheduled->value] ?? 0)
                ->description('Dalam tahap interview')
                ->descriptionIcon('heroicon-m-calendar')
                ->color(ApplicationStatus::InterviewScheduled->color()),
            Stat::make('Diterima Perusahaan', $stats[ApplicationStatus::CompanyAccepted->value] ?? 0)
                ->description('Lamaran lolos seleksi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color(ApplicationStatus::CompanyAccepted->color()),
            Stat::make('Ditarik', $stats[ApplicationStatus::Withdrawn->value] ?? 0)
                ->description('Lamaran yang ditarik')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color(ApplicationStatus::Withdrawn->color()),
        ];
    }
}
