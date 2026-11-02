<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $semester = Semester::current();

        $courses = Course::with(['department', 'teacher'])
            ->withCount(['enrollments as enrolled_count' => fn ($q) => $q
                ->where('status', 'Enrolled')
                ->when($semester, fn ($w) => $w->where('semester_id', $semester->id))])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w->where('title', 'like', $term)->orWhere('code', 'like', $term));
            })
            ->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->department_id))
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('courses.index', [
            'courses'     => $courses,
            'departments' => Department::orderBy('name')->get(),
            'semester'    => $semester,
        ]);
    }

    public function create()
    {
        return view('courses.form', $this->formData(new Course(['credit_hours' => 3, 'capacity' => 30, 'tuition_fee' => 0])));
    }

    public function store(Request $request): RedirectResponse
    {
        Course::create($this->validated($request));

        return redirect()->route('courses.index')->with('success', 'Course created.');
    }

    public function edit(Course $course)
    {
        return view('courses.form', $this->formData($course));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $course->update($this->validated($request, $course));

        return redirect()->route('courses.index')->with('success', 'Course updated.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->enrollments()->exists()) {
            return back()->with('error', 'This course has enrollment records and cannot be deleted.');
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted.');
    }

    private function formData(Course $course): array
    {
        return [
            'course'            => $course,
            'departmentOptions' => Department::orderBy('name')->pluck('name', 'id'),
            'teacherOptions'    => Teacher::orderBy('name')->pluck('name', 'id'),
        ];
    }

    private function validated(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'teacher_id'    => ['nullable', 'exists:teachers,id'],
            'code'          => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($course)],
            'title'         => ['required', 'string', 'max:255'],
            'credit_hours'  => ['required', 'integer', 'between:1,10'],
            'capacity'      => ['required', 'integer', 'between:1,500'],
            'tuition_fee'   => ['required', 'numeric', 'between:0,99999'],
        ]);
    }
}
