@extends('layouts.school')
@section('title', $semester->exists ? 'Edit semester' : 'New semester')
@section('content')
<h1 class="h3 mb-3">{{ $semester->exists ? 'Edit semester' : 'New semester' }}</h1>
<div class="card" style="max-width:640px"><div class="card-body p-4">
    <form method="POST" action="{{ $semester->exists ? route('semesters.update', $semester) : route('semesters.store') }}">
        @csrf
        @if ($semester->exists) @method('PUT') @endif
        <x-field name="name" label="Semester name (e.g. Semester 1 2026)" :value="$semester->name" required autofocus />
        <div class="row">
            <x-field class="col-md-6" name="start_date" label="Start date" type="date" :value="$semester->start_date?->format('Y-m-d')" required />
            <x-field class="col-md-6" name="end_date" label="End date" type="date" :value="$semester->end_date?->format('Y-m-d')" required />
        </div>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $semester->is_active))>
            <label class="form-check-label" for="is_active">Active semester <span class="text-muted small">(only one can be active; this one replaces the current)</span></label>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('semesters.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div></div>
@endsection
