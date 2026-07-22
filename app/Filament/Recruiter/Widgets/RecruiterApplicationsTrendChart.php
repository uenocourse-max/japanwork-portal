<?php

namespace App\Filament\Recruiter\Widgets;

use App\Models\JobApplication;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecruiterApplicationsTrendChart extends ChartWidget
{
    protected ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Tren Lamaran Masuk';

    protected ?string $description = 'Jumlah lamaran dalam 30 hari terakhir';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $userId = auth()->id();

        $data = Cache::remember('recruiter_applications_trend_'.$userId, 300, function () use ($userId) {
            $startDate = Carbon::now()->subDays(29)->startOfDay();

            $results = JobApplication::whereHas('jobListing', fn ($q) => $q->where('posted_by', $userId))
                ->where('applied_at', '>=', $startDate)
                ->selectRaw('DATE(applied_at) as date, COUNT(*) as total')
                ->groupBy(DB::raw('DATE(applied_at)'))
                ->pluck('total', 'date')
                ->toArray();

            $daily = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $daily[$date] = $results[$date] ?? 0;
            }

            return $daily;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Lamaran',
                    'data' => array_values($data),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => array_map(fn ($date) => Carbon::parse($date)->format('d/m'), array_keys($data)),
        ];
    }
}
