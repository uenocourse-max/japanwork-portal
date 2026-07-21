<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        $applications = JobApplication::where('student_id', $student->id)
            ->with('jobListing.sswCategory')
            ->latest('applied_at')
            ->get();

        $stats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'reviewed' => $applications->where('status', 'reviewed')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'interview_scheduled' => $applications->where('status', 'interview_scheduled')->count(),
            'company_accepted' => $applications->where('status', 'company_accepted')->count(),
            'not_passed' => $applications->where('status', 'not_passed')->count(),
        ];

        $recentApplications = $applications->take(5);

        $unreadNotifications = $user->unreadNotifications->count();

        return view('student.dashboard.index', compact(
            'student',
            'stats',
            'recentApplications',
            'unreadNotifications',
        ));
    }
}
