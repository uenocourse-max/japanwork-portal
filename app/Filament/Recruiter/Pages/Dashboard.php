<?php

namespace App\Filament\Recruiter\Pages;

use App\Filament\Recruiter\Widgets\RecruiterApplicationsChart;
use App\Filament\Recruiter\Widgets\RecruiterApplicationsTrendChart;
use App\Filament\Recruiter\Widgets\RecruiterStatsOverview;
use App\Filament\Recruiter\Widgets\RecruiterTopJobsWidget;
use App\Filament\Recruiter\Widgets\RecruiterUpcomingInterviewsWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.recruiter.pages.dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    protected static ?int $navigationSort = -1;

    public function getWidgets(): array
    {
        return [
            RecruiterStatsOverview::class,
            RecruiterApplicationsChart::class,
            RecruiterApplicationsTrendChart::class,
            RecruiterUpcomingInterviewsWidget::class,
            RecruiterTopJobsWidget::class,
        ];
    }
}
