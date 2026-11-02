@extends('layouts.school')
@section('title', 'Courses')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-0">Courses</h1>
        <div class="small text-muted">Seats counted for: {{ $semester->name ?? 'all semesters (no active semester)' }}</div>
    </div>
    <a href="{{ route('courses.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add course</a>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-6"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search course code or title"></div>
        <div class="col-md-3">
            <select name="department_id" class="form-select" onchange="this.form.submit()">
                <option value="">All departments</option>
                @foreach ($departments as $d)
                    <option value="{{ $d->id }}" @selected(request('department_id') == $d->id)>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Course</th><th>Department</th><th>Instructor</th><th>Credits</th><th style="width:18%">Seats</th><th>Tuition</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse ($courses as $c)
            @php $pct = $c->capacity ? min(100, round($c->enrolled_count / $c->capacity * 100)) : 0; @endphp
            <tr>
                <td><div class="fw-medium">{{ $c->title }}</div><div class="small text-muted">{{ $c->code }}</div></td>
                <td><span class="badge text-bg-light border">{{ $c->department->name }}</span></td>
                <td>{{ $c->teacher?->name ?? '—' }}</td>
                <td>{{ $c->credit_hours }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bar flex-fill"><i style="--w: {{ $pct }}%; {{ $pct >= 100 ? 'background:var(--bad)' : '' }}"></i></div>
                        <span class="small fw-semibold">{{ $c->enrolled_count }}/{{ $c->capacity }}</span>
                    </div>
                </td>
                <td>${{ number_format($c->tuition_fee, 2) }}</td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('courses.edit', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('courses.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete this course?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-5">No courses found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Showing {{ $courses->firstItem() ?? 0 }}–{{ $courses->lastItem() ?? 0 }} of {{ $courses->total() }}</small>
    {{ $courses->links() }}
</div>
@endsection
