<?php

namespace App\Filament\Recruiter\Pages;

use App\Models\JobApplication;
use App\Models\JobListing;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.recruiter.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getRecentApplications()
    {
        return JobApplication::whereHas('jobListing', fn ($q) => $q->where('posted_by', auth()->id()))
            ->with('student', 'jobListing')
            ->latest('applied_at')
            ->limit(5)
            ->get();
    }

    public function getMyRecentJobs()
    {
        return JobListing::where('posted_by', auth()->id())
            ->withCount('applications')
            ->latest()
            ->limit(5)
            ->get();
    }
}
