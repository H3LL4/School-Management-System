<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::with('teacher')->withCount('students')
            ->when($request->filled('q'), fn($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->when($request->filled('grade'), fn($q) => $q->where('grade', $request->grade))
            ->when($request->filled('teacher_id'), fn($q) => $q->where('teacher_id', $request->teacher_id))
            ->orderBy('grade')->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('classrooms.index', [
            'classrooms' => $classrooms,
            'teachers'   => Teacher::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('classrooms.form', [
            'classroom' => new Classroom(),
            'teachers'  => Teacher::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Classroom::create($this->validated($request));

        return redirect()->route('classrooms.index')->with('success', 'Classroom created.');
    }

    public function edit(Classroom $classroom)
    {
        return view('classrooms.form', [
            'classroom' => $classroom,
            'teachers'  => Teacher::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        $classroom->update($this->validated($request, $classroom));

        return redirect()->route('classrooms.index')->with('success', 'Classroom updated.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        if ($classroom->students()->exists()) {
            return back()->with('error', 'This classroom still has students. Move or delete them first.');
        }

        $classroom->delete();

        return redirect()->route('classrooms.index')->with('success', 'Classroom deleted.');
    }

    private function validated(Request $request, ?Classroom $classroom = null): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:255', Rule::unique('classrooms', 'name')->ignore($classroom)],
            'grade'      => ['required', 'integer', 'between:1,12'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
        ]);
    }
}
