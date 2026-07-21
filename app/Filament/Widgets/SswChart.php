<?php

namespace App\Filament\Widgets;

use App\Models\SswCategory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SswChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Statistik Kategori SSW';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = Cache::remember('widget_ssw_chart', 300, function () {
            return SswCategory::withCount('students')
                ->orderByDesc('students_count')
                ->get()
                ->pluck('students_count', 'name')
                ->toArray();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => array_values($data),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => array_keys($data),
        ];
    }
}
