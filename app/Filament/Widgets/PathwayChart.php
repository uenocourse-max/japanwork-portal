<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class PathwayChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Statistik Jalur';
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = Cache::remember('widget_pathway_chart', 300, function () {
            return Student::select('pathway')
                ->selectRaw('count(*) as total')
                ->groupBy('pathway')
                ->pluck('total', 'pathway')
                ->toArray();
        });

        $labels = [
            'mandiri' => 'Mandiri',
            'lpk' => 'LPK',
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => ['#3b82f6', '#22c55e'],
                ],
            ],
            'labels' => array_map(fn ($key) => $labels[$key] ?? $key, array_keys($data)),
        ];
    }
}
