<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::withCount('courses')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $term)->orWhere('code', 'like', $term));
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.form', ['department' => new Department()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Department::create($this->validated($request));

        return redirect()->route('departments.index')->with('success', 'Department created.');
    }

    public function edit(Department $department)
    {
        return view('departments.form', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $department->update($this->validated($request, $department));

        return redirect()->route('departments.index')->with('success', 'Department updated.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->courses()->exists()) {
            return back()->with('error', 'This department still has courses. Move or delete them first.');
        }

        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Department deleted.');
    }

    private function validated(Request $request, ?Department $department = null): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('departments', 'name')->ignore($department)],
            'code'        => ['required', 'string', 'max:10', Rule::unique('departments', 'code')->ignore($department)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
