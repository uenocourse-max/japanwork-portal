<?php

namespace App\Filament\Widgets;

use App\Enums\MatchingStatus;
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
        $matched = $stats[MatchingStatus::Matched->value] ?? 0;
        $processMatching = $stats[MatchingStatus::ProcessMatching->value] ?? 0;
        $waitingResult = $stats[MatchingStatus::WaitingResult->value] ?? 0;
        $notMatched = $stats[MatchingStatus::NotMatched->value] ?? 0;

        return [
            Stat::make('Total Siswa', $total)
                ->description('Jumlah seluruh siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Matched', $matched)
                ->description('Siswa yang sudah matched')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color(MatchingStatus::Matched->color()),
            Stat::make('Proses Matching', $processMatching)
                ->description('Siswa dalam proses matching')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color(MatchingStatus::ProcessMatching->color()),
            Stat::make('Menunggu Hasil', $waitingResult)
                ->description('Siswa menunggu hasil')
                ->descriptionIcon('heroicon-m-clock')
                ->color(MatchingStatus::WaitingResult->color()),
            Stat::make('Belum Matching', $notMatched)
                ->description('Siswa belum matching')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color(MatchingStatus::NotMatched->color()),
        ];
    }
}
