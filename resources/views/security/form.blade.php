@extends('layouts.school')
@section('title', $user->exists ? 'Edit account' : 'Create account')
@section('content')
<h1 class="h3 mb-3">{{ $user->exists ? 'Edit account' : 'Create account' }}</h1>
<div class="card" style="max-width:640px"><div class="card-body p-4">
    <form method="POST" action="{{ $user->exists ? route('security.update', $user) : route('security.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif
        <x-field name="name" label="Full name" :value="$user->name" required autofocus />
        <x-field name="email" label="Email address" type="email" :value="$user->email" required />
        <x-select name="role" label="Role" :options="\App\Models\User::ROLES" :selected="$user->role" :disabled="$user->exists && $user->id === auth()->id()" required />
        @if ($user->exists && $user->id === auth()->id())
            <p class="text-muted small">You cannot change your own role.</p>
        @endif
        @unless ($user->exists)
            <div class="row">
                <x-field class="col-md-6" name="password" label="Password" type="password" minlength="8" required />
                <x-field class="col-md-6" name="password_confirmation" label="Confirm password" type="password" minlength="8" required />
            </div>
        @endunless
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('security.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div></div>
@endsection
