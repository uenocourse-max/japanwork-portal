<?php

namespace App\Providers\Filament;

use App\Filament\Recruiter\Pages\Dashboard;
use App\Filament\Recruiter\Widgets\RecruiterApplicationsChart;
use App\Filament\Recruiter\Widgets\RecruiterApplicationsTrendChart;
use App\Filament\Recruiter\Widgets\RecruiterStatsOverview;
use App\Filament\Recruiter\Widgets\RecruiterTopJobsWidget;
use App\Filament\Recruiter\Widgets\RecruiterUpcomingInterviewsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class RecruiterPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('recruiter')
            ->path('recruiter')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Recruiter/Resources'), for: 'App\Filament\Recruiter\Resources')
            ->discoverPages(in: app_path('Filament/Recruiter/Pages'), for: 'App\Filament\Recruiter\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Recruiter/Widgets'), for: 'App\Filament\Recruiter\Widgets')
            ->widgets([
                RecruiterStatsOverview::class,
                RecruiterApplicationsChart::class,
                RecruiterApplicationsTrendChart::class,
                RecruiterUpcomingInterviewsWidget::class,
                RecruiterTopJobsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
