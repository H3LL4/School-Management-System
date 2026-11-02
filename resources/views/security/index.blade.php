@extends('layouts.school')
@section('title', 'Security & Access')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-0">Security &amp; Access</h1>
        <div class="small text-muted">Locks after {{ config('school.login_max_failed_attempts') }} failed sign-ins for {{ config('school.login_lockout_minutes') }} minutes.</div>
    </div>
    <a href="{{ route('security.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Create account</a>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-5"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name or email"></div>
        <div class="col-md-3">
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">All roles</option>
                @foreach (\App\Models\User::ROLES as $key => $label)<option value="{{ $key }}" @selected(request('role') === $key)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="locked" @selected(request('status') === 'locked')>Locked</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a href="{{ route('security.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Account</th><th>Role</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @foreach ($users as $u)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar" style="--h: {{ crc32($u->name) % 360 }}">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                        <div><div class="fw-medium">{{ $u->name }}@if ($u->id === auth()->id()) <span class="badge text-bg-light border">you</span>@endif</div><div class="small text-muted">{{ $u->email }}</div></div>
                    </div>
                </td>
                <td><span class="badge text-bg-light border">{{ $u->role_label }}</span></td>
                <td>
                    @if ($u->isLocked())
                        <span class="badge text-bg-danger"><i class="bi bi-lock-fill me-1"></i>Locked · {{ $u->lockMinutesLeft() }} min</span>
                    @elseif ($u->failed_attempts > 0)
                        <span class="badge text-bg-warning">{{ $u->failed_attempts }} failed attempt(s)</span>
                    @else
                        <span class="badge text-bg-success">Active</span>
                    @endif
                </td>
                <td class="text-end text-nowrap">
                    @if ($u->isLocked() || $u->failed_attempts > 0)
                        <form method="POST" action="{{ route('security.unlock', $u) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-secondary" title="Unlock"><i class="bi bi-unlock"></i></button>
                        </form>
                    @endif
                    <a href="{{ route('security.password', $u) }}" class="btn btn-sm btn-outline-secondary" title="Reset password"><i class="bi bi-key"></i></a>
                    <a href="{{ route('security.edit', $u) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                    @if ($u->id !== auth()->id())
                        <form method="POST" action="{{ route('security.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Delete this account?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
