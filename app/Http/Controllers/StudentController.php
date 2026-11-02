<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classroom.teacher')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn($w) => $w->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->when($request->filled('classroom_id'), fn($q) => $q->where('classroom_id', $request->classroom_id))
            ->when($request->filled('gender'), fn($q) => $q->where('gender', $request->gender))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('students.index', [
            'students'   => $students,
            'classrooms' => Classroom::orderBy('grade')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('students.form', [
            'student'    => new Student(['enrollment_date' => today()]),
            'classrooms' => Classroom::orderBy('grade')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Student::create($this->validated($request));

        return redirect()->route('students.index')->with('success', 'Student registered.');
    }

    public function edit(Student $student)
    {
        return view('students.form', [
            'student'    => $student,
            'classrooms' => Classroom::orderBy('grade')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validated($request, $student));

        return redirect()->route('students.index')->with('success', 'Student updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }

    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student)],
            'gender'          => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'dob'             => ['required', 'date', 'before:today'],
            'enrollment_date' => ['required', 'date'],
            'classroom_id'    => ['required', 'exists:classrooms,id'],
            'description'     => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
