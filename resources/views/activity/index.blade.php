@extends('layouts.school')
@section('title', 'Activity Log')
@section('content')
<h1 class="h3 mb-3">Activity Log</h1>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-6"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by user or description"></div>
        <div class="col-md-3">
            <select name="action" class="form-select" onchange="this.form.submit()">
                <option value="">All actions</option>
                @foreach ($actions as $a)<option value="{{ $a }}" @selected(request('action') === $a)>{{ str_replace('_', ' ', $a) }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a href="{{ route('activity.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>When</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr></thead>
        <tbody>
        @forelse ($logs as $log)
            <tr>
                <td class="text-nowrap"><div>{{ $log->created_at->format('M d, H:i') }}</div><div class="small text-muted">{{ $log->created_at->diffForHumans() }}</div></td>
                <td class="fw-medium">{{ $log->username ?? 'system' }}</td>
                <td><span class="badge text-bg-{{ $log->action_class }}">{{ str_replace('_', ' ', $log->action) }}</span></td>
                <td>{{ $log->description }}</td>
                <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-5">No activity recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }}</small>
    {{ $logs->links() }}
</div>
@endsection
