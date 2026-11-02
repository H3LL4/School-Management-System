@extends('layouts.school')
@section('title', 'Semesters')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Semesters</h1>
    <a href="{{ route('semesters.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add semester</a>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-9"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search semester name"></div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a href="{{ route('semesters.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Name</th><th>Dates</th><th>Status</th><th>Enrollments</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse ($semesters as $s)
            <tr>
                <td class="fw-medium">{{ $s->name }}</td>
                <td>{{ $s->start_date->format('M d, Y') }} – {{ $s->end_date->format('M d, Y') }}</td>
                <td>@if ($s->is_active)<span class="badge text-bg-success">Active</span>@else<span class="badge text-bg-light border">Closed</span>@endif</td>
                <td><a href="{{ route('enrollments.index', ['semester_id' => $s->id]) }}">{{ $s->enrollments_count }}</a></td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('semesters.edit', $s) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('semesters.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Delete this semester?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-5">No semesters found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $semesters->links() }}</div>
@endsection
