<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $enrollments = Enrollment::with(['student', 'course.department', 'semester'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w
                    ->whereHas('student', fn ($s) => $s->where('name', 'like', $term))
                    ->orWhereHas('course', fn ($c) => $c->where('title', 'like', $term)->orWhere('code', 'like', $term)));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('semester_id'), fn ($q) => $q->where('semester_id', $request->semester_id))
            ->when($request->filled('course_id'), fn ($q) => $q->where('course_id', $request->course_id))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('enrollments.index', [
            'enrollments' => $enrollments,
            'semesters'   => Semester::orderByDesc('start_date')->get(),
            'courses'     => Course::orderBy('code')->get(),
        ]);
    }

    public function create()
    {
        return view('enrollments.form', [
            'enrollment'      => new Enrollment(['semester_id' => Semester::current()?->id, 'status' => 'Enrolled']),
            'studentOptions'  => Student::orderBy('name')->pluck('name', 'id'),
            'courseOptions'   => Course::orderBy('code')->get()->mapWithKeys(fn ($c) => [$c->id => $c->code . ' · ' . $c->title . ' (cap ' . $c->capacity . ')']),
            'semesterOptions' => Semester::orderByDesc('start_date')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id'  => [
                'required', 'exists:students,id',
                Rule::unique('enrollments', 'student_id')
                    ->where(fn ($q) => $q->where('course_id', $request->course_id)->where('semester_id', $request->semester_id)),
            ],
            'course_id'   => ['required', 'exists:courses,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'status'      => ['required', Rule::in(Enrollment::STATUSES)],
        ], [
            'student_id.unique' => 'This student already has an enrollment for this course and semester.',
        ]);

        DB::transaction(function () use ($data) {
            $this->assertCapacity($data['course_id'], $data['semester_id'], $data['status']);
            Enrollment::create($data);
        });

        return redirect()->route('enrollments.index')->with('success', 'Enrollment saved.');
    }

    public function edit(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'course', 'semester']);

        return view('enrollments.form', compact('enrollment'));
    }

    /** Only the status can change after creation (e.g. promote Waitlisted to Enrolled). */
    public function update(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(Enrollment::STATUSES)]]);

        DB::transaction(function () use ($data, $enrollment) {
            $this->assertCapacity($enrollment->course_id, $enrollment->semester_id, $data['status'], $enrollment);
            $enrollment->update($data);
        });

        return redirect()->route('enrollments.index')->with('success', 'Enrollment updated.');
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        if ($enrollment->payments()->exists()) {
            return back()->with('error', 'This enrollment has payments. Delete the payments first.');
        }

        $enrollment->delete();

        return redirect()->route('enrollments.index')->with('success', 'Enrollment deleted.');
    }

    /**
     * Capacity rule: a student cannot be ENROLLED once the course is full for that semester.
     * WAITLISTED and DROPPED skip the check. The course row is locked so two clerks
     * cannot take the last seat at the same moment.
     */
    private function assertCapacity(int|string $courseId, int|string $semesterId, string $status, ?Enrollment $ignore = null): void
    {
        if ($status !== 'Enrolled') {
            return;
        }

        $course = Course::lockForUpdate()->findOrFail($courseId);

        $taken = Enrollment::where('course_id', $course->id)
            ->where('semester_id', $semesterId)
            ->where('status', 'Enrolled')
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))
            ->count();

        if ($taken >= $course->capacity) {
            throw ValidationException::withMessages([
                'status' => "{$course->code} is full ({$taken}/{$course->capacity}) for this semester. Choose Waitlisted instead.",
            ]);
        }
    }
}
