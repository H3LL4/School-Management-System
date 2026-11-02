<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        $dept = [];
        foreach ([['Science', 'SCI'], ['Mathematics', 'MTH'], ['Languages', 'LNG']] as [$name, $code]) {
            $dept[$code] = Department::create(['name' => $name, 'code' => $code, 'description' => $name . ' department']);
        }

        $teachers = Teacher::orderBy('id')->get();
        $t = fn (int $i) => $teachers[$i % max(1, $teachers->count())]->id ?? null;

        $course = [];
        foreach ([
            ['MTH101', 'Algebra I',         'MTH', 3, 3, 120, 0],   // small capacities so you can test the waitlist
            ['MTH201', 'Geometry',          'MTH', 3, 2, 120, 0],
            ['SCI101', 'Physics Basics',    'SCI', 4, 3, 150, 1],
            ['SCI102', 'Chemistry Basics',  'SCI', 4, 30, 150, 1],
            ['LNG101', 'English Grammar',   'LNG', 2, 30, 90, 2],
        ] as [$code, $title, $d, $credits, $cap, $fee, $ti]) {
            $course[$code] = Course::create([
                'department_id' => $dept[$d]->id, 'teacher_id' => $t($ti), 'code' => $code, 'title' => $title,
                'credit_hours' => $credits, 'capacity' => $cap, 'tuition_fee' => $fee,
            ]);
        }

        $prev = Semester::create([
            'name' => 'Semester 1 ' . today()->year,
            'start_date' => today()->subMonths(8)->startOfMonth(), 'end_date' => today()->subMonths(4)->endOfMonth(),
            'is_active' => false,
        ]);
        $curr = Semester::create([
            'name' => 'Semester 2 ' . today()->year,
            'start_date' => today()->subMonths(2)->startOfMonth(), 'end_date' => today()->addMonths(2)->endOfMonth(),
            'is_active' => true,
        ]);

        $s = Student::orderBy('id')->take(8)->get()->values();
        if ($s->count() < 7) {
            return;   // run SchoolSeeder first
        }

        $enrol = fn ($student, $code, $sem, $status = 'Enrolled') => Enrollment::create([
            'student_id' => $student->id, 'course_id' => $course[$code]->id, 'semester_id' => $sem->id, 'status' => $status,
        ]);

        // Current semester
        $e1 = $enrol($s[0], 'MTH101', $curr);
        $e2 = $enrol($s[1], 'MTH101', $curr);
        $e3 = $enrol($s[2], 'MTH101', $curr);
        $enrol($s[3], 'MTH101', $curr, 'Waitlisted');
        $enrol($s[0], 'MTH201', $curr);
        $enrol($s[1], 'MTH201', $curr);
        $enrol($s[2], 'MTH201', $curr, 'Waitlisted');
        $e4 = $enrol($s[3], 'SCI101', $curr);
        $enrol($s[4], 'SCI101', $curr);
        $e5 = $enrol($s[0], 'LNG101', $curr);
        $enrol($s[5], 'LNG101', $curr);
        $enrol($s[6], 'LNG101', $curr);

        // Previous semester
        $e6 = $enrol($s[0], 'SCI102', $prev);
        $enrol($s[1], 'SCI102', $prev);

        // Payments (amounts never exceed the tuition fee)
        foreach ([
            [$e1, 120, 'Cash', 'Paid', 3],
            [$e2, 60, 'Bank Transfer', 'Paid', 5],
            [$e3, 120, 'Card', 'Pending', 1],
            [$e4, 150, 'Card', 'Paid', 7],
            [$e5, 90, 'Cash', 'Paid', 2],
            [$e6, 150, 'Cash', 'Paid', 120],
        ] as [$enrollment, $amount, $method, $status, $daysAgo]) {
            Payment::create([
                'enrollment_id' => $enrollment->id, 'amount' => $amount, 'method' => $method,
                'status' => $status, 'paid_at' => today()->subDays($daysAgo),
            ]);
        }
    }
}
