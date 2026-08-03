<?php

namespace App\Filament\Recruiter\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class RecruiterApplicationsChart extends ChartWidget
{
    protected ?string $pollingInterval = null;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Distribusi Lamaran';

    protected ?string $description = 'Status lamaran yang masuk ke lowongan Anda';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $userId = auth()->id();

        $data = Cache::remember('recruiter_applications_chart_'.$userId, 300, function () use ($userId) {
            return JobApplication::whereHas('jobListing', fn ($q) => $q->where('posted_by', $userId))
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        });

        $labels = ApplicationStatus::options();

        $colors = [
            '#f59e0b', // pending - warning
            '#3b82f6', // reviewed - blue
            '#22c55e', // accepted - success
            '#8b5cf6', // interview_scheduled - purple
            '#10b981', // company_accepted - emerald
            '#ef4444', // not_passed - red
            '#6b7280', // rejected - gray
            '#94a3b8', // withdrawn - slate
        ];

        $filteredLabels = [];
        $filteredData = [];
        $filteredColors = [];

        foreach ($labels as $key => $label) {
            if (isset($data[$key]) && $data[$key] > 0) {
                $filteredLabels[] = $label;
                $filteredData[] = $data[$key];
                $filteredColors[] = $colors[array_search($key, array_keys($labels))];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $filteredData,
                    'backgroundColor' => $filteredColors,
                ],
            ],
            'labels' => $filteredLabels,
        ];
    }
}
