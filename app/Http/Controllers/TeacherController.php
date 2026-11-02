<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::withCount('classrooms')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn($w) => $w->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('subject', 'like', $term));
            })
            ->when($request->filled('gender'), fn($q) => $q->where('gender', $request->gender))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('teachers.form', ['teacher' => new Teacher()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Teacher::create($this->validated($request));

        return redirect()->route('teachers.index')->with('success', 'Teacher added.');
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.form', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $teacher->update($this->validated($request, $teacher));

        return redirect()->route('teachers.index')->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        // Classrooms keep existing; their teacher becomes empty (nullOnDelete).
        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Teacher deleted.');
    }

    private function validated(Request $request, ?Teacher $teacher = null): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', Rule::unique('teachers', 'email')->ignore($teacher)],
            'phone'   => ['nullable', 'string', 'max:30'],
            'gender'  => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'subject' => ['required', 'string', 'max:255'],
        ]);
    }
}
