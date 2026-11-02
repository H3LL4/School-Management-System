<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SecurityAccessController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Everything below needs a login. Admin and Staff can both use the school pages.
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('students', StudentController::class)->except('show');
    Route::resource('classrooms', ClassroomController::class)->except('show');
    Route::resource('teachers', TeacherController::class)->except('show');
    Route::resource('departments', DepartmentController::class)->except('show');
    Route::resource('courses', CourseController::class)->except('show');
    Route::resource('semesters', SemesterController::class)->except('show');
    Route::resource('enrollments', EnrollmentController::class)->except('show');
    Route::resource('payments', PaymentController::class)->except('show');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::prefix('accounts/security-access')->name('security.')->group(function () {
            Route::get('/', [SecurityAccessController::class, 'index'])->name('index');
            Route::get('/create', [SecurityAccessController::class, 'create'])->name('create');
            Route::post('/', [SecurityAccessController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [SecurityAccessController::class, 'edit'])->name('edit');
            Route::put('/{user}', [SecurityAccessController::class, 'update'])->name('update');
            Route::patch('/{user}/unlock', [SecurityAccessController::class, 'unlock'])->name('unlock');
            Route::get('/{user}/password', [SecurityAccessController::class, 'passwordForm'])->name('password');
            Route::put('/{user}/password', [SecurityAccessController::class, 'resetPassword'])->name('password.update');
            Route::delete('/{user}', [SecurityAccessController::class, 'destroy'])->name('destroy');
        });

        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.index');
    });

    // Breeze profile pages
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Breeze login / register / logout routes
require __DIR__ . '/auth.php';
