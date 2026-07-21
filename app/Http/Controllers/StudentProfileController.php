<?php

namespace App\Http\Controllers;

use App\Models\SswCategory;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StudentProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student || ! $student->isProfileComplete()) {
            return redirect()->route('student.profile.edit');
        }

        return view('student.profile.show', compact('student'));
    }

    public function edit()
    {
        $user = Auth::user();
        $student = $user->student;
        $sswCategories = SswCategory::all();

        return view('student.profile.edit', compact('student', 'sswCategories'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer|min:15|max:60',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'address' => 'required|string',
            'height_cm' => 'required|integer|min:100|max:250',
            'weight_kg' => 'required|integer|min:30|max:200',
            'blood_type' => 'required|in:A,B,AB,O',
            'gender' => 'required|in:male,female',
            'marital_status' => 'required|in:single,married',
            'phone_number' => ['required', 'string', Rule::unique('students', 'phone_number')->ignore($user->student?->id, 'id')],
            'participant_status' => 'required|in:ex,new_comer',
            'jft_score' => 'nullable|integer|min:0|max:480',
            'jlpt_level' => 'nullable|in:N5,N4,N3,N2,N1,JFT Basic A2',
            'japanese_learning_months' => 'required|integer|min:0',
            'pathway' => 'required|in:mandiri,lpk',
            'lpk_name' => 'required_if:pathway,lpk|nullable|string|max:255',
            'matching_status' => 'required|in:not_matched,process_matching,waiting_result,matched,cancelled',
            'matched_company_name' => 'required_if:matching_status,matched|nullable|string|max:255',
            'photo_drive_url' => 'nullable|url',
            'cv_drive_url' => 'nullable|url',
            'ssw_categories' => 'required|array|min:1',
            'ssw_categories.*' => 'exists:ssw_categories,id',
        ]);

        $studentData = $request->only([
            'full_name', 'age', 'birth_place', 'birth_date', 'address',
            'height_cm', 'weight_kg', 'blood_type', 'gender', 'marital_status',
            'phone_number', 'participant_status', 'jft_score', 'jlpt_level',
            'japanese_learning_months', 'pathway', 'lpk_name',
            'matching_status', 'matched_company_name',
            'photo_drive_url', 'cv_drive_url',
        ]);

        if ($request->pathway === 'mandiri') {
            $studentData['lpk_name'] = null;
        }

        if ($request->matching_status !== 'matched') {
            $studentData['matched_company_name'] = null;
        }

        $student = $user->student ?? new Student;
        $student->fill($studentData);
        $student->user_id = $user->id;
        $student->save();

        $student->sswCategories()->sync($request->ssw_categories);

        return redirect()->route('student.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword()
    {
        return view('student.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
