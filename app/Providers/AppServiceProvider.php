<?php

namespace App\Providers;

use App\Http\Middleware\EnsureRole;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use App\Observers\ActivityObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Lets routes say ->middleware('role:officer')
        Route::aliasMiddleware('role', EnsureRole::class);

        // Audit trail for every create / update / delete on these models
        foreach ([
            Student::class, Classroom::class, Teacher::class, Department::class,
            Course::class, Semester::class, Enrollment::class, Payment::class,
        ] as $model) {
            $model::observe(ActivityObserver::class);
        }

        // Audit trail for sign-in and sign-out
        Event::listen(Login::class, function (Login $event) {
            ActivityLog::record('login', 'Signed in', $event->user);
        });
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                ActivityLog::record('logout', 'Signed out', $event->user);
            }
        });
    }
}
