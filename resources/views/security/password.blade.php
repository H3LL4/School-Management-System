@extends('layouts.school')
@section('title', 'Reset password')
@section('content')
<h1 class="h3 mb-3">Reset password</h1>
<div class="card" style="max-width:560px"><div class="card-body p-4">
    <p class="mb-3"><strong>{{ $user->name }}</strong><br><span class="text-muted">{{ $user->email }}</span></p>
    <form method="POST" action="{{ route('security.password.update', $user) }}">
        @csrf @method('PUT')
        <x-field name="password" label="New password" type="password" minlength="8" required autofocus />
        <x-field name="password_confirmation" label="Confirm new password" type="password" minlength="8" required />
        <p class="text-muted small">Resetting the password also unlocks the account.</p>
        <button class="btn btn-primary">Reset password</button>
        <a href="{{ route('security.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div></div>
@endsection
