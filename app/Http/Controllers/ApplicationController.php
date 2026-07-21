<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $applications = JobApplication::where('student_id', $student->id)
            ->with('jobListing.sswCategory')
            ->latest('applied_at')
            ->paginate(10);

        return view('student.applications.index', compact('applications'));
    }

    public function notifications(Request $request)
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->paginate(20);

        return view('student.notifications.index', compact('notifications'));
    }

    public function markAllNotificationsRead(Request $request)
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back();
    }

    public function withdraw(JobApplication $application)
    {
        $student = auth()->user()->student;

        if ($application->student_id !== $student->id) {
            abort(403);
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Lamaran sudah direview dan tidak bisa ditarik.');
        }

        $application->update(['status' => 'withdrawn']);

        return back()->with('success', 'Lamaran berhasil ditarik.');
    }
}
