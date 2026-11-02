@extends('layouts.school')
@section('title', $department->exists ? 'Edit department' : 'New department')
@section('content')
<h1 class="h3 mb-3">{{ $department->exists ? 'Edit department' : 'New department' }}</h1>
<div class="card" style="max-width:640px"><div class="card-body p-4">
    <form method="POST" action="{{ $department->exists ? route('departments.update', $department) : route('departments.store') }}">
        @csrf
        @if ($department->exists) @method('PUT') @endif
        <div class="row">
            <x-field class="col-md-8" name="name" label="Department name" :value="$department->name" required autofocus />
            <x-field class="col-md-4" name="code" label="Code (e.g. SCI)" :value="$department->code" maxlength="10" required />
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $department->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div></div>
@endsection
