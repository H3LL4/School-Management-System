@extends('layouts.school')
@section('title', 'Enrollments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Enrollments</h1>
    <a href="{{ route('enrollments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New enrollment</a>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-3"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search student or course"></div>
        <div class="col-md-3">
            <select name="semester_id" class="form-select" onchange="this.form.submit()">
                <option value="">All semesters</option>
                @foreach ($semesters as $s)<option value="{{ $s->id }}" @selected(request('semester_id') == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="course_id" class="form-select" onchange="this.form.submit()">
                <option value="">All courses</option>
                @foreach ($courses as $c)<option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ $c->code }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach (\App\Models\Enrollment::STATUSES as $st)<option @selected(request('status') === $st)>{{ $st }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>ID</th><th>Student</th><th>Course</th><th>Semester</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse ($enrollments as $e)
            <tr>
                <td>{{ $e->id }}</td>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar sm" style="--h: {{ crc32($e->student->name) % 360 }}">{{ mb_strtoupper(mb_substr($e->student->name, 0, 1)) }}</span>
                        <div class="fw-medium">{{ $e->student->name }}</div>
                    </div>
                </td>
                <td><div>{{ $e->course->title }}</div><div class="small text-muted">{{ $e->course->code }} · {{ $e->course->department->name }}</div></td>
                <td>{{ $e->semester->name }}</td>
                <td><span class="badge text-bg-{{ $e->status_class }}">{{ $e->status }}</span></td>
                <td class="text-end text-nowrap">
                    @if ($e->status === 'Enrolled')
                        <a href="{{ route('payments.create', ['enrollment_id' => $e->id]) }}" class="btn btn-sm btn-outline-secondary write-only" title="Record payment"><i class="bi bi-cash-coin"></i></a>
                    @endif
                    <a href="{{ route('enrollments.edit', $e) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('enrollments.destroy', $e) }}" class="d-inline" onsubmit="return confirm('Delete this enrollment?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-5">No enrollments found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Showing {{ $enrollments->firstItem() ?? 0 }}–{{ $enrollments->lastItem() ?? 0 }} of {{ $enrollments->total() }}</small>
    {{ $enrollments->links() }}
</div>
@endsection
