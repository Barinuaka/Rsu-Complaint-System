<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

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
    $user = Auth::user();

    // Fetch complaints assigned to THIS specific HOD user (by user ID, not role ID)
    $complaints = Complaint::with(['category', 'campus'])
        ->where('campus_id', $user->campus_id)
        ->where('assigned_to', $user->id)
        ->whereIn('current_status', [
            'submitted', 'assigned', 'pending',
            'in_progress', 'in_review', 'escalated'
        ])
        ->orderBy('sla_deadline_at', 'asc')
        ->get();

    return view('dashboards.hod', compact('complaints'));
})->middleware(['auth'])->name('hod.dashboard');

// Course Adviser dashboard
Route::get('/adviser/dashboard', function () {
    $user = Auth::user();

    // Fetch complaints assigned to THIS specific Course Adviser (by user ID)
    $complaints = Complaint::with(['category', 'campus'])
        ->where('campus_id', $user->campus_id)
        ->where('assigned_to', $user->id)
        ->whereIn('current_status', [
            'submitted', 'assigned', 'in_review'
        ])
        ->orderBy('sla_deadline_at', 'asc')
        ->get();

    return view('dashboards.adviser', compact('complaints'));
})->middleware(['auth'])->name('adviser.dashboard');

// Dean dashboard
Route::get('/dean/dashboard', function () {
    $user = Auth::user();

    // Dean sees escalated complaints from their campus
    $complaints = Complaint::with(['category', 'campus'])
        ->where('campus_id', $user->campus_id)
        ->whereIn('current_status', ['escalated'])
        ->orderBy('sla_deadline_at', 'asc')
        ->get();

    return view('dashboards.dean', compact('complaints'));
})->middleware(['auth'])->name('dean.dashboard');

// Admin dashboard
// Admin dashboard
Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])
     ->middleware(['auth'])
     ->name('admin.dashboard');

// Complaint routes (authenticated users only)
Route::middleware(['auth'])->group(function () {
    Route::get('/complaints/submit', [App\Http\Controllers\ComplaintController::class, 'create'])
         ->name('complaints.create');
    Route::post('/complaints/submit', [App\Http\Controllers\ComplaintController::class, 'store'])
         ->name('complaints.store');
    Route::get('/complaints/confirmation/{token}', [App\Http\Controllers\ComplaintController::class, 'confirmation'])
         ->name('complaints.confirmation');
});

// Public complaint tracking — no login required
Route::get('/track', [App\Http\Controllers\ComplaintController::class, 'track'])
     ->name('complaints.track');

// Complaint action routes (for officers)
Route::middleware(['auth'])->group(function () {
    Route::get('/complaints/{id}/detail', [App\Http\Controllers\ComplaintController::class, 'detail'])
         ->name('complaints.detail');
    Route::post('/complaints/{id}/update-status', [App\Http\Controllers\ComplaintController::class, 'updateStatus'])
         ->name('complaints.updateStatus');
    Route::post('/complaints/{id}/resolve', [App\Http\Controllers\ComplaintController::class, 'resolve'])
         ->name('complaints.resolve');
    Route::post('/complaints/{id}/escalate', [App\Http\Controllers\ComplaintController::class, 'escalate'])
         ->name('complaints.escalate');
});


// Admin user management routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])
         ->name('admin.users');
    Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])
         ->name('admin.users.create');
    Route::post('/users/create', [App\Http\Controllers\AdminController::class, 'storeUser'])
         ->name('admin.users.store');
    Route::post('/users/{id}/toggle-status', [App\Http\Controllers\AdminController::class, 'toggleStatus'])
         ->name('admin.users.toggle');
});


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