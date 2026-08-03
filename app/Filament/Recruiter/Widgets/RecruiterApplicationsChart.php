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

        $filteredLabels = [];
        $filteredData = [];
        $filteredColors = [];

        foreach ($labels as $key => $label) {
            if (isset($data[$key]) && $data[$key] > 0) {
                $filteredLabels[] = $label;
                $filteredData[] = $data[$key];
                $filteredColors[] = ApplicationStatus::tryFrom($key)?->hexColor() ?? '#9ca3af';
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
