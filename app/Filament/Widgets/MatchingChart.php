<?php

namespace App\Filament\Widgets;

use App\Enums\MatchingStatus;
use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class MatchingChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Statistik Matching';
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $data = Cache::remember('widget_matching_chart', 300, function () {
            return Student::select('matching_status')
                ->selectRaw('count(*) as total')
                ->groupBy('matching_status')
                ->pluck('total', 'matching_status')
                ->toArray();
        });

        $labels = MatchingStatus::options();

        $backgroundColor = collect(array_keys($data))
            ->map(fn (string $status): string => MatchingStatus::tryFrom($status)?->hexColor() ?? '#9ca3af')
            ->values()
            ->all();

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => $backgroundColor,
                ],
            ],
            'labels' => array_map(fn ($key) => $labels[$key] ?? $key, array_keys($data)),
        ];
    }
}
