<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isStudent()) {
            return redirect()->route('student.dashboard');
        }

        return view('auth.student.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                Auth::logout();

                return back()->withErrors(['email' => 'Akun ini adalah akun admin. Silakan login di /admin.']);
            }

            if ($user->isRecruiter()) {
                Auth::logout();

                return back()->withErrors(['email' => 'Akun ini adalah akun recruiter. Silakan login di /recruiter/login.']);
            }

            $request->session()->regenerate();

            if ($user->student && $user->student->isProfileComplete()) {
                return redirect()->route('student.dashboard');
            }

            return redirect()->route('student.profile.edit');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
