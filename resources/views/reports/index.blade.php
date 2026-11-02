@extends('layouts.school')
@section('title', 'Reports')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Reports</h1>
    <a href="{{ route('reports.index', array_filter(['type' => $type, 'semester_id' => $semesterId, 'export' => 1])) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Export CSV</a>
</div>

<ul class="nav nav-pills gap-1 mb-3">
    @foreach ($types as $key => $label)
        <li class="nav-item">
            <a class="nav-link {{ $type === $key ? 'active' : 'bg-white border' }}" href="{{ route('reports.index', array_filter(['type' => $key, 'semester_id' => $semesterId])) }}">{{ $label }}</a>
        </li>
    @endforeach
</ul>

@if ($type !== 'semester')
<form method="GET" class="card card-body mb-3">
    <input type="hidden" name="type" value="{{ $type }}">
    <div class="row g-2 align-items-center">
        <div class="col-md-4">
            <select name="semester_id" class="form-select" onchange="this.form.submit()">
                <option value="">All semesters</option>
                @foreach ($semesters as $s)<option value="{{ $s->id }}" @selected((string) $semesterId === (string) $s->id)>{{ $s->name }}{{ $s->is_active ? ' (active)' : '' }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto"><a href="{{ route('reports.index', ['type' => $type]) }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>
@endif

<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr>@foreach ($headings as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse ($rows as $row)
            <tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
        @empty
            <tr><td colspan="{{ count($headings) }}" class="text-center text-muted py-5">No data for this report yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
@endsection
