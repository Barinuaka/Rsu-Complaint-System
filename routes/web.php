<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Main dashboard router — redirects based on role
Route::get('/dashboard', function () {
    $user = auth()->user()->load('role');
    return match(true) {
        $user->hasRole('System Administrator') => redirect()->route('admin.dashboard'),
        $user->hasRole('Dean')                 => redirect()->route('dean.dashboard'),
        $user->hasRole('Head of Department')   => redirect()->route('hod.dashboard'),
        $user->hasRole('Course Adviser')       => redirect()->route('adviser.dashboard'),
        $user->hasRole('Lecturer')             => redirect()->route('lecturer.dashboard'),
        $user->hasRole('Student')              => redirect()->route('student.dashboard'),
        default                                => view('dashboard')
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Student dashboard
Route::get('/student/dashboard', function () {
    return view('dashboards.student');
})->middleware(['auth'])->name('student.dashboard');

// Lecturer dashboard
Route::get('/lecturer/dashboard', function () {
    return view('dashboards.lecturer');
})->middleware(['auth'])->name('lecturer.dashboard');

// HOD dashboard
Route::get('/hod/dashboard', function () {
    return view('dashboards.hod');
})->middleware(['auth'])->name('hod.dashboard');

// Course Adviser dashboard
Route::get('/adviser/dashboard', function () {
    return view('dashboards.adviser');
})->middleware(['auth'])->name('adviser.dashboard');

// Dean dashboard
Route::get('/dean/dashboard', function () {
    return view('dashboards.dean');
})->middleware(['auth'])->name('dean.dashboard');

// Admin dashboard
Route::get('/admin/dashboard', function () {
    return view('dashboards.admin');
})->middleware(['auth'])->name('admin.dashboard');

// Complaint routes
Route::middleware(['auth'])->group(function () {
    Route::get('/complaints/submit', [App\Http\Controllers\ComplaintController::class, 'create'])
         ->name('complaints.create');
    Route::post('/complaints/submit', [App\Http\Controllers\ComplaintController::class, 'store'])
         ->name('complaints.store');
    Route::get('/complaints/confirmation/{token}', [App\Http\Controllers\ComplaintController::class, 'confirmation'])
         ->name('complaints.confirmation');
});

// Public tracking — no login required
Route::get('/track', [App\Http\Controllers\ComplaintController::class, 'track'])
     ->name('complaints.track');

// Profile routes (required by Breeze navigation bar)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
         ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
         ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
         ->name('profile.destroy');
});

require __DIR__.'/auth.php';