# Upgrade: features from the old CEMS project -> School Management System

Adds roles, Security & Access, account lockout, activity log, departments, courses,
semesters, enrollments (capacity + waitlist), payments and reports (CSV export).
Nothing you already use is removed: students, classrooms and teachers keep working.

## Step 1. Copy the files
Copy everything in this zip into your Laravel project folder (merge folders).
These files REPLACE existing ones:
- app/Models/User.php
- app/Providers/AppServiceProvider.php
- app/Http/Requests/Auth/LoginRequest.php   (Breeze login, now with lockout)
- app/Http/Controllers/DashboardController.php
- routes/web.php
- database/seeders/DatabaseSeeder.php
- resources/views/dashboard.blade.php

Everything else in the zip is new.

## Step 2. Five small manual edits to files you already have

### 2a. app/Models/Student.php  (add the relationship)
Add this `use` line at the top with the others:
    use Illuminate\Database\Eloquent\Relations\HasMany;
Add this method inside the class:
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

### 2b. app/Http/Controllers/StudentController.php  (protect students that have enrollments)
Replace the destroy() method with:
    public function destroy(Student $student): RedirectResponse
    {
        if ($student->enrollments()->exists()) {
            return back()->with('error', 'This student has course enrollments. Remove those first.');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }

### 2c. resources/views/layouts/school.blade.php  (sidebar menu by role)
Find the whole block that starts with  <nav class="side-nav">  and ends with  </nav>
and replace it with:

    <nav class="side-nav">
        @php
            $u = auth()->user();
            $ops = $u->hasRole('officer', 'manager');
            $it  = $u->hasRole('it_support');
            $links = [
                ['dashboard', 'Dashboard', 'bi-grid-1x2-fill', 'dashboard', true],
                ['students.index', 'Students', 'bi-people-fill', 'students.*', $ops],
                ['classrooms.index', 'Classrooms', 'bi-door-open-fill', 'classrooms.*', $ops],
                ['teachers.index', 'Teachers', 'bi-person-workspace', 'teachers.*', $ops],
                ['departments.index', 'Departments', 'bi-diagram-3-fill', 'departments.*', $ops],
                ['courses.index', 'Courses', 'bi-journal-bookmark-fill', 'courses.*', $ops],
                ['semesters.index', 'Semesters', 'bi-calendar3', 'semesters.*', $ops],
                ['enrollments.index', 'Enrollments', 'bi-card-checklist', 'enrollments.*', $ops],
                ['payments.index', 'Payments', 'bi-cash-coin', 'payments.*', $ops],
                ['reports.index', 'Reports', 'bi-bar-chart-line-fill', 'reports.*', $ops],
                ['security.index', 'Security & Access', 'bi-shield-lock-fill', 'security.*', $it],
                ['activity.index', 'Activity Log', 'bi-clock-history', 'activity.*', $it],
            ];
        @endphp
        @foreach ($links as [$route, $label, $icon, $match, $show])
            @if ($show)
                <a href="{{ route($route) }}" class="side-link {{ request()->routeIs($match) ? 'active' : '' }}"><i class="bi {{ $icon }}"></i><span>{{ $label }}</span></a>
            @endif
        @endforeach
    </nav>

Then, in the same file, make these three small changes:

1) Let the menu scroll on small screens. Find:
    .side-nav{display:grid;gap:.25rem}
   Replace with:
    .side-nav{display:grid;gap:.25rem;align-content:start;overflow-y:auto;min-height:0;margin-bottom:1rem}

2) Show the role under the name. Find:
    <small>View profile</small>
   Replace with:
    <small>{{ auth()->user()->role_label }}</small>

3) Read-only mode for managers (hides Add / Edit / Delete buttons; the server also blocks them).
   Find the line:   <body>
   Replace with:    <body class="{{ auth()->user()->isReadOnly() ? 'read-only' : '' }}">
   Then paste this CSS just above the line that starts with  @media (prefers-reduced-motion
   (inside the <style> block):
    .read-only a[href$="/create"],.read-only a[href$="/edit"],.read-only .table form.d-inline,.read-only .write-only{display:none!important}

## Step 3. Run the migrations and seed the data
Fresh start (recommended while developing; this ERASES the current data and reseeds):
    php artisan migrate:fresh --seed
    php artisan optimize:clear
    php artisan serve

Keep your current data instead:
    php artisan migrate
    php artisan optimize:clear
(The oldest user is promoted to superuser automatically. Everyone else becomes "Manager (read-only)"
 until IT/Support changes the role on the Security & Access page. New self-registered users
 also start as read-only managers.)
Course, enrollment and payment sample data only come with the fresh seed.

## Demo logins (all passwords: 123456)
| Email               | Role                                   |
|---------------------|----------------------------------------|
| admin@gmail.com     | IT / Support (superuser, sees everything) |
| officer@gmail.com   | Registration Officer (read + write)     |


## Optional .env settings
    LOGIN_MAX_FAILED_ATTEMPTS=3
    LOGIN_LOCKOUT_MINUTES=15

## What to test (and show in the demo video)
1. Log in as officer: add a department, a course (capacity 2), a semester (tick Active).
2. Enrollments: enrol 2 students as Enrolled, then a 3rd as Enrolled -> blocked, choose Waitlisted.
   Try enrolling the same student in the same course + semester twice -> blocked.
3. Payments: record a payment from the Enrollments page (cash icon). Try paying more than the fee -> blocked.
4. Reports: switch tabs, pick a semester, press Export CSV.
5. Log out. Try 3 wrong passwords for officer@gmail.com: the counter counts down, then the account locks.
   Even the correct password is refused while locked.
6. Log in as admin: Security & Access -> Unlock, Reset Password, Create Account, Delete. Open Activity Log.
7. Log in as manager: everything is visible, no Add / Edit / Delete buttons. Typing /students/create in the
   address bar shows "You can't open this page".
