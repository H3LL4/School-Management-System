<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::withCount('enrollments')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->orderByDesc('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('semesters.index', compact('semesters'));
    }

    public function create()
    {
        return view('semesters.form', ['semester' => new Semester(['start_date' => today(), 'end_date' => today()->addMonths(4)])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $semester = Semester::create($data);
            $this->keepOnlyOneActive($semester);
        });

        return redirect()->route('semesters.index')->with('success', 'Semester created.');
    }

    public function edit(Semester $semester)
    {
        return view('semesters.form', compact('semester'));
    }

    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $data = $this->validated($request, $semester);

        DB::transaction(function () use ($data, $semester) {
            $semester->update($data);
            $this->keepOnlyOneActive($semester);
        });

        return redirect()->route('semesters.index')->with('success', 'Semester updated.');
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        if ($semester->enrollments()->exists()) {
            return back()->with('error', 'This semester has enrollment records and cannot be deleted.');
        }

        $semester->delete();

        return redirect()->route('semesters.index')->with('success', 'Semester deleted.');
    }

    /** Only one semester can be the active one. */
    private function keepOnlyOneActive(Semester $semester): void
    {
        if ($semester->is_active) {
            Semester::where('id', '!=', $semester->id)->update(['is_active' => false]);
        }
    }

    private function validated(Request $request, ?Semester $semester = null): array
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255', Rule::unique('semesters', 'name')->ignore($semester)],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
