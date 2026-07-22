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

        $stats = [
            'total' => JobApplication::where('student_id', $student->id)->count(),
            'pending' => JobApplication::where('student_id', $student->id)->where('status', 'pending')->count(),
            'reviewed' => JobApplication::where('student_id', $student->id)->where('status', 'reviewed')->count(),
            'accepted' => JobApplication::where('student_id', $student->id)->where('status', 'accepted')->count(),
            'interview_scheduled' => JobApplication::where('student_id', $student->id)->where('status', 'interview_scheduled')->count(),
            'company_accepted' => JobApplication::where('student_id', $student->id)->where('status', 'company_accepted')->count(),
            'not_passed' => JobApplication::where('student_id', $student->id)->where('status', 'not_passed')->count(),
        ];

        $recentApplications = JobApplication::where('student_id', $student->id)
            ->with('jobListing.sswCategory')
            ->latest('applied_at')
            ->limit(5)
            ->get();

        $unreadNotifications = $user->unreadNotifications->count();

        return view('student.dashboard.index', compact(
            'student',
            'stats',
            'recentApplications',
            'unreadNotifications',
        ));
    }
}
