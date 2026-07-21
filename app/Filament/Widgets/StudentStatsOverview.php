<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StudentStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = Cache::remember('widget_student_stats', 300, function () {
            return Student::selectRaw('matching_status, COUNT(*) as total')
                ->groupBy('matching_status')
                ->pluck('total', 'matching_status')
                ->toArray();
        });

        $total = array_sum($stats);
        $matched = $stats['matched'] ?? 0;
        $processMatching = $stats['process_matching'] ?? 0;
        $waitingResult = $stats['waiting_result'] ?? 0;
        $notMatched = $stats['not_matched'] ?? 0;

        return [
            Stat::make('Total Siswa', $total)
                ->description('Jumlah seluruh siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Matched', $matched)
                ->description('Siswa yang sudah matched')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Proses Matching', $processMatching)
                ->description('Siswa dalam proses matching')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),
            Stat::make('Menunggu Hasil', $waitingResult)
                ->description('Siswa menunggu hasil')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
            Stat::make('Belum Matching', $notMatched)
                ->description('Siswa belum matching')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
