<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/doctors/{doctor}', [PageController::class, 'doctor'])->name('doctors.show');

Route::get('/dashboard', function () {
    $user = request()->user();

    return match ($user->role) {
        'patient' => redirect()->route('patient.dashboard'),
        'doctor' => redirect()->route('doctor.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => abort(403),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::prefix('/patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/doctors', [PatientDashboardController::class, 'doctors'])->name('doctors');
        Route::get('/appointments', [PatientDashboardController::class, 'appointments'])->name('appointments');
        Route::get('/reviews', [PatientDashboardController::class, 'reviews'])->name('reviews');
        Route::get('/prescriptions', [PatientDashboardController::class, 'prescriptions'])->name('prescriptions');
    });

    Route::prefix('/doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [DoctorDashboardController::class, 'appointments'])->name('appointments');
        Route::get('/reviews', [DoctorDashboardController::class, 'reviews'])->name('reviews');
        Route::get('/patients', [DoctorDashboardController::class, 'patients'])->name('patients');
    });

    Route::prefix('/admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/doctors', [AdminController::class, 'doctors'])->name('doctors');
        Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
        Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
        Route::post('/doctors', [AdminController::class, 'storeDoctor'])->name('doctors.store');
        Route::patch('/doctors/{doctor}/status', [AdminController::class, 'updateDoctorStatus'])->name('doctors.status');
    });

    Route::post('/doctors/{doctor}/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::post('/doctors/{doctor}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
