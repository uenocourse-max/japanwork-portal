<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class JlptChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Statistik JLPT';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = Cache::remember('widget_jlpt_chart', 300, function () {
            return Student::whereNotNull('jlpt_level')
                ->select('jlpt_level')
                ->selectRaw('count(*) as total')
                ->groupBy('jlpt_level')
                ->pluck('total', 'jlpt_level')
                ->toArray();
        });

        $levels = ['N5', 'N4', 'N3', 'N2', 'N1', 'JFT Basic A2'];
        $orderedData = [];
        foreach ($levels as $level) {
            $orderedData[$level] = $data[$level] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => array_values($orderedData),
                    'backgroundColor' => ['#94a3b8', '#22d3ee', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                ],
            ],
            'labels' => array_keys($orderedData),
        ];
    }
}
