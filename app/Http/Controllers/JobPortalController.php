<?php

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Models\JobListing;
use App\Models\SswCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobPortalController extends Controller
{
    public function index(Request $request)
    {
        $query = JobListing::query()->available()
            ->with('sswCategory', 'poster')
            ->withCount('applications');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($request->search).'%'])
                    ->orWhereRaw('LOWER(company_name) LIKE ?', ['%'.mb_strtolower($request->search).'%']);
            });
        }

        if ($request->filled('ssw_category_id')) {
            $query->where('ssw_category_id', $request->ssw_category_id);
        }

        if ($request->filled('jlpt_level')) {
            $query->where('jlpt_level_required', $request->jlpt_level);
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->filled('location')) {
            $query->whereRaw('LOWER(location) LIKE ?', ['%'.mb_strtolower($request->location).'%']);
        }

        $view = in_array($request->input('view'), ['detail', 'compact', 'list', 'grid']) ? $request->input('view') : 'detail';

        $jobs = $query->latest()->paginate(12)->withQueryString();

        $sswCategories = SswCategory::orderBy('name')->withCount(['jobListings' => fn ($q) => $q->available()])->get();

        $totalJobs = $sswCategories->sum('job_listings_count');

        $popularJobs = JobListing::query()->available()
            ->with('sswCategory')
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->limit(5)
            ->get();

        return view('portal.index', compact('jobs', 'sswCategories', 'totalJobs', 'view', 'popularJobs'));
    }

    public function show(JobListing $job)
    {
        if ($job->status !== JobStatus::Open->value || ($job->deadline && $job->deadline->isPast())) {
            abort(404);
        }

        $job->loadCount('applications');
        $job->load('sswCategory', 'poster');

        $relatedJobs = JobListing::query()->available()->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                $q->where('ssw_category_id', $job->ssw_category_id)
                    ->orWhereRaw('LOWER(location) LIKE ?', ['%'.mb_strtolower($job->location).'%']);
            })
            ->with('sswCategory')
            ->latest()
            ->limit(4)
            ->get();

        $sswMismatch = false;
        $isSaved = false;
        if (Auth::check()) {
            $student = Auth::user()->student;
            $sswMismatch = $student && $job->job_type === JobType::TokuteiGinou->value && ! $student->hasSswCategory($job->ssw_category_id);
            $isSaved = $student && $student->savedJobs()->where('job_listing_id', $job->id)->exists();
        }

        return view('portal.show', compact('job', 'relatedJobs', 'sswMismatch', 'isSaved'));
    }
}
