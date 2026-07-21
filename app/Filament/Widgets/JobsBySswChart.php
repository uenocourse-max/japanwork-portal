<?php

namespace App\Filament\Widgets;

use App\Models\JobListing;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class JobsBySswChart extends ChartWidget
{
    protected static ?int $sort = 4;

    public function getHeading(): ?string
    {
        return 'Lowongan per SSW';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = Cache::remember('widget_jobs_by_ssw', 300, function () {
            return JobListing::where('status', 'open')
                ->whereNotNull('ssw_category_id')
                ->join('ssw_categories', 'job_listings.ssw_category_id', '=', 'ssw_categories.id')
                ->selectRaw('ssw_categories.name as category, COUNT(*) as total')
                ->groupBy('ssw_categories.name')
                ->pluck('total', 'category')
                ->toArray();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Lowongan',
                    'data' => array_values($data),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => array_keys($data),
        ];
    }
}
