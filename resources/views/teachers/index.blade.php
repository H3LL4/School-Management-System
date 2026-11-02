@extends('layouts.school')
@section('title', 'Teachers')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Teachers</h1>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Add
            teacher</a>
    </div>

    <form method="GET" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-6"><input type="search" name="q" value="{{ request('q') }}" class="form-control"
                    placeholder="Search name, email or subject"></div>
            <div class="col-md-3">
                <select name="gender" class="form-select" onchange="this.form.submit()">
                    <option value="">All genders</option>
                    @foreach (['Male', 'Female', 'Other'] as $g)
                        <option @selected(request('gender') === $g)>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a
                    href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Reset</a></div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Teacher</th>
                        <th>Contact</th>
                        <th>Subject</th>
                        <th>Classrooms</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>
                                <div class="fw-medium">{{ $t->name }}</div>
                                <div class="small text-muted">{{ $t->gender }}</div>
                            </td>
                            <td>
                                <div>{{ $t->email }}</div>
                                <div class="small text-muted">{{ $t->phone ?: '—' }}</div>
                            </td>
                            <td><span class="badge text-bg-light border">{{ $t->subject }}</span></td>
                            <td>{{ $t->classrooms_count }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('teachers.edit', $t) }}" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('teachers.destroy', $t) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this teacher?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No teachers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $teachers->links() }}</div>
@endsection