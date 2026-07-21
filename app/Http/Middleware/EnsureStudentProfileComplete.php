<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isStudent()) {
            $student = $user->student;

            if (! $student || ! $student->isProfileComplete()) {
                if ($request->routeIs('student.profile.edit', 'student.profile.update')) {
                    return $next($request);
                }

                return redirect()->route('student.profile.edit');
            }
        }

        return $next($request);
    }
}
