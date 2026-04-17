<?php

use App\Http\Controllers\AboutMeController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-me', [AboutMeController::class, 'index'])->name('about-me');

Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{course:slug}', [CourseController::class, 'show'])->name('show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/courses/{course:slug}/enrollments', [CourseEnrollmentController::class, 'store'])->name('courses.enrollments.store');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [OtpController::class, 'showLogin'])->name('login');
    Route::post('login/send-otp', [OtpController::class, 'sendOtp'])->name('login.send-otp');
    Route::post('login/verify-otp', [OtpController::class, 'verifyOtp'])->name('login.verify-otp');
});

require __DIR__ . '/settings.php';
