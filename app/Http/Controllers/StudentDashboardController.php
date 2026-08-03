<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            abort(403, 'Akses ditolak.');
        }

        $stats = [
            'total' => JobApplication::where('student_id', $student->id)->count(),
            'pending' => JobApplication::where('student_id', $student->id)->where('status', ApplicationStatus::Pending->value)->count(),
            'reviewed' => JobApplication::where('student_id', $student->id)->where('status', ApplicationStatus::Reviewed->value)->count(),
            'accepted' => JobApplication::where('student_id', $student->id)->where('status', ApplicationStatus::Accepted->value)->count(),
            'interview_scheduled' => JobApplication::where('student_id', $student->id)->where('status', ApplicationStatus::InterviewScheduled->value)->count(),
            'company_accepted' => JobApplication::where('student_id', $student->id)->where('status', ApplicationStatus::CompanyAccepted->value)->count(),
            'not_passed' => JobApplication::where('student_id', $student->id)->where('status', ApplicationStatus::NotPassed->value)->count(),
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
