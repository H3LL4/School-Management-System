<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'studentCount'   => Student::count(),
            'teacherCount'   => Teacher::count(),
            'classroomCount' => Classroom::count(),
            'courseCount'    => Course::count(),
            'classrooms'     => Classroom::withCount('students')->orderBy('grade')->orderBy('name')->get(),
            'recentStudents' => Student::with('classroom')->latest('id')->limit(6)->get(),
        ]);
    }
}