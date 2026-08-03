<?php

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\SswCategory;
use App\Notifications\NewApplicationReceived;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobListing::query()->available()
            ->with('sswCategory')
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

        $savedJobIds = auth()->user()->student->savedJobs()->pluck('job_listing_id');

        return view('student.jobs.index', compact('jobs', 'sswCategories', 'view', 'savedJobIds'));
    }

    public function show(JobListing $job)
    {
        if ($job->status !== JobStatus::Open->value || ($job->deadline && $job->deadline->isPast())) {
            abort(404);
        }

        $job->loadCount('applications');
        $job->load('sswCategory', 'poster');

        $student = auth()->user()->student;
        $application = null;

        if ($student) {
            $application = JobApplication::where('job_listing_id', $job->id)
                ->where('student_id', $student->id)
                ->first();
        }

        $hasApplied = (bool) $application;
        $sswMismatch = $student && $job->job_type === JobType::TokuteiGinou->value && ! $student->hasSswCategory($job->ssw_category_id);
        $isSaved = $student && $student->savedJobs()->where('job_listing_id', $job->id)->exists();

        return view('student.jobs.show', compact('job', 'hasApplied', 'application', 'sswMismatch', 'isSaved'));
    }

    public function apply(JobListing $job)
    {
        if ($job->status !== JobStatus::Open->value || ($job->deadline && $job->deadline->isPast())) {
            return back()->with('error', 'Lowongan ini sudah tidak tersedia.');
        }

        $user = auth()->user();
        $student = $user->student;

        if (! $student || ! $student->isProfileComplete()) {
            return redirect()->route('student.profile.edit')
                ->with('error', 'Silakan lengkapi profil terlebih dahulu sebelum melamar.');
        }

        if (JobApplication::where('job_listing_id', $job->id)->where('student_id', $student->id)->exists()) {
            return back()->with('error', 'Anda sudah melamar untuk lowongan ini.');
        }

        $application = JobApplication::create([
            'job_listing_id' => $job->id,
            'student_id' => $student->id,
            'applied_at' => now(),
        ]);

        if ($job->poster) {
            $job->poster->notify(new NewApplicationReceived($application));
        }

        return back()->with('success', 'Lamaran berhasil dikirim!');
    }
}
