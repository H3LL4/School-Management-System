@extends('layouts.school')
@section('title', 'Payments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-0">Payments</h1>
        <div class="small text-muted">Paid total for this filter: <strong class="text-body">${{ number_format($totalPaid, 2) }}</strong></div>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Record payment</a>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-3"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search reference or student"></div>
        <div class="col-md-3">
            <select name="semester_id" class="form-select" onchange="this.form.submit()">
                <option value="">All semesters</option>
                @foreach ($semesters as $s)<option value="{{ $s->id }}" @selected(request('semester_id') == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach (\App\Models\Payment::STATUSES as $st)<option @selected(request('status') === $st)>{{ $st }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="method" class="form-select" onchange="this.form.submit()">
                <option value="">All methods</option>
                @foreach (\App\Models\Payment::METHODS as $m)<option @selected(request('method') === $m)>{{ $m }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Reference</th><th>Student</th><th>Course</th><th>Amount</th><th>Method</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse ($payments as $p)
            <tr>
                <td><div class="fw-medium">{{ $p->reference }}</div><div class="small text-muted">{{ $p->paid_at->format('M d, Y') }}</div></td>
                <td>{{ $p->enrollment->student->name }}</td>
                <td><div>{{ $p->enrollment->course->code }}</div><div class="small text-muted">{{ $p->enrollment->semester->name }}</div></td>
                <td class="fw-semibold">${{ number_format($p->amount, 2) }}</td>
                <td>{{ $p->method }}</td>
                <td><span class="badge text-bg-{{ $p->status_class }}">{{ $p->status }}</span></td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('payments.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('payments.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Delete this payment?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-5">No payments found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Showing {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}</small>
    {{ $payments->links() }}
</div>
@endsection
