@extends('layouts.school')
@section('title', 'Departments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Departments</h1>
    <a href="{{ route('departments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add department</a>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-9"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search department name or code"></div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>ID</th><th>Name</th><th>Code</th><th>Description</th><th>Courses</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse ($departments as $d)
            <tr>
                <td>{{ $d->id }}</td>
                <td class="fw-medium">{{ $d->name }}</td>
                <td><span class="badge text-bg-light border">{{ $d->code }}</span></td>
                <td class="text-muted">{{ \Illuminate\Support\Str::limit($d->description, 60) ?: '—' }}</td>
                <td><a href="{{ route('courses.index', ['department_id' => $d->id]) }}">{{ $d->courses_count }}</a></td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('departments.edit', $d) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('departments.destroy', $d) }}" class="d-inline" onsubmit="return confirm('Delete this department?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-5">No departments found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $departments->links() }}</div>
@endsection
