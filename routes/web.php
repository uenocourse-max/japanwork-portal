<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Auth\StudentRegisterController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobPortalController;
use App\Http\Controllers\SavedJobController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Middleware\EnsureStudentProfileComplete;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('portal.index'));

// Public job portal (no auth required)
Route::get('/jobs', [JobPortalController::class, 'index'])->name('portal.index');
Route::get('/jobs/{job}', [JobPortalController::class, 'show'])->name('portal.show');

// Auth apply (redirects to login if guest)
Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])
    ->name('portal.apply')
    ->middleware(['auth', EnsureStudentProfileComplete::class]);

Route::middleware('guest')->group(function () {
    Route::get('/register', [StudentRegisterController::class, 'showRegistrationForm'])->name('student.register');
    Route::post('/register', [StudentRegisterController::class, 'register'])->name('student.register.store');
    Route::get('/login', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
    Route::post('/login', [StudentLoginController::class, 'login'])->name('student.login.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [StudentLoginController::class, 'logout'])->name('student.logout')->middleware('auth');

Route::middleware(['auth', EnsureStudentProfileComplete::class])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [StudentProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
    Route::get('/password', [StudentProfileController::class, 'changePassword'])->name('password');
    Route::put('/password', [StudentProfileController::class, 'updatePassword'])->name('password.update');

    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');
    Route::post('/jobs/{job}/bookmark', [SavedJobController::class, 'toggle'])->name('jobs.bookmark');
    Route::get('/bookmarks', [SavedJobController::class, 'index'])->name('bookmarks.index');
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications/{application}/withdraw', [ApplicationController::class, 'withdraw'])->name('applications.withdraw');
    Route::get('/notifications', [ApplicationController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [ApplicationController::class, 'markAllNotificationsRead'])->name('notifications.markAllRead');
});
