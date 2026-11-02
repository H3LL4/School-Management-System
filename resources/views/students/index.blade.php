@extends('layouts.school')
@section('title', 'Students')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Student Directory</h1>
        <a href="{{ route('students.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add student</a>
    </div>

    <form method="GET" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-5"><input type="search" name="q" value="{{ request('q') }}" class="form-control"
                    placeholder="Search students by name or email"></div>
            <div class="col-md-3">
                <select name="classroom_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All classrooms</option>
                    @foreach ($classrooms as $c)
                        <option value="{{ $c->id }}" @selected(request('classroom_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="gender" class="form-select" onchange="this.form.submit()">
                    <option value="">All genders</option>
                    @foreach (['Male', 'Female', 'Other'] as $g)
                        <option @selected(request('gender') === $g)>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a
                    href="{{ route('students.index') }}" class="btn btn-outline-secondary">Reset</a></div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Age / Enrolled</th>
                        <th>Classroom</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar"
                                        style="--h: {{ crc32($s->name) % 360 }}">{{ mb_strtoupper(mb_substr($s->name, 0, 1)) }}</span>
                                    <div>
                                        <div class="fw-medium">{{ $s->name }}</div>
                                        <div class="small text-muted">{{ $s->gender }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $s->email }}</div>
                                <div class="small text-muted">{{ $s->description ?: 'N/A' }}</div>
                            </td>
                            <td>
                                <div>{{ $s->dob->age }} yrs</div>
                                <div class="small text-muted">since {{ $s->enrollment_date->format('M d, Y') }}</div>
                            </td>
                            <td><span class="badge text-bg-light border">{{ $s->classroom->name }}</span>
                                <div class="small text-muted">{{ $s->classroom->teacher?->name }}</div>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('students.edit', $s) }}" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('students.destroy', $s) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this student?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of
            {{ $students->total() }} entries</small>
        {{ $students->links() }}
    </div>
@endsection