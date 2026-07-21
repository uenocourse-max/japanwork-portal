<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Models\SavedJob;

class SavedJobController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $savedJobs = $student->savedJobs()
            ->with('jobListing.sswCategory')
            ->latest()
            ->paginate(12);

        return view('student.saved-jobs.index', compact('savedJobs'));
    }

    public function toggle(JobListing $job)
    {
        $student = auth()->user()->student;

        $saved = SavedJob::where('student_id', $student->id)
            ->where('job_listing_id', $job->id)
            ->first();

        if ($saved) {
            $saved->delete();

            return back()->with('success', 'Lowongan dihapus dari bookmark.');
        }

        SavedJob::create([
            'student_id' => $student->id,
            'job_listing_id' => $job->id,
        ]);

        return back()->with('success', 'Lowongan disimpan ke bookmark.');
    }
}
