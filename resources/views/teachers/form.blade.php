@extends('layouts.school')
@section('title', $teacher->exists ? 'Edit teacher' : 'New teacher')
@section('content')
    <h1 class="h3 mb-3">{{ $teacher->exists ? 'Edit teacher' : 'New teacher' }}</h1>
    <div class="card" style="max-width:760px">
        <div class="card-body p-4">
            <form method="POST"
                action="{{ $teacher->exists ? route('teachers.update', $teacher) : route('teachers.store') }}">
                @csrf
                @if ($teacher->exists) @method('PUT') @endif
                <div class="row">
                    <x-field class="col-md-6" name="name" label="Full name" :value="$teacher->name" required autofocus />
                    <x-field class="col-md-6" name="email" label="Email" type="email" :value="$teacher->email" required />
                    <x-field class="col-md-4" name="phone" label="Phone" :value="$teacher->phone" />
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror"
                            required>
                            <option value="">Select…</option>
                            @foreach (['Male', 'Female', 'Other'] as $g)
                                <option @selected(old('gender', $teacher->gender) === $g)>{{ $g }}</option>
                            @endforeach
                        </select>
                        @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <x-field class="col-md-4" name="subject" label="Subject" :value="$teacher->subject" required />
                </div>
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection