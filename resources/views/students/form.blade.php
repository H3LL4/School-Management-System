@extends('layouts.school')
@section('title', $student->exists ? 'Edit student' : 'Student Registration')
@section('content')
    <h1 class="h3 mb-3">{{ $student->exists ? 'Edit student' : 'Student Registration' }}</h1>
    <div class="card" style="max-width:760px">
        <div class="card-body p-4">
            <form method="POST"
                action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}">
                @csrf
                @if ($student->exists) @method('PUT') @endif
                <div class="row">
                    <x-field class="col-md-6" name="name" label="Student name" :value="$student->name" required autofocus />
                    <x-field class="col-md-6" name="email" label="Email address" type="email" :value="$student->email"
                        required />
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror"
                            required>
                            <option value="">Select…</option>
                            @foreach (['Male', 'Female', 'Other'] as $g)
                                <option @selected(old('gender', $student->gender) === $g)>{{ $g }}</option>
                            @endforeach
                        </select>
                        @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <x-field class="col-md-4" name="dob" label="Date of birth" type="date"
                        :value="$student->dob?->format('Y-m-d')" required />
                    <x-field class="col-md-4" name="enrollment_date" label="Enrollment date" type="date"
                        :value="$student->enrollment_date?->format('Y-m-d')" required />
                    <div class="col-12 mb-3">
                        <label for="classroom_id" class="form-label">Classroom</label>
                        <select id="classroom_id" name="classroom_id"
                            class="form-select @error('classroom_id') is-invalid @enderror" required>
                            <option value="">Select classroom…</option>
                            @foreach ($classrooms as $c)
                                <option value="{{ $c->id }}" @selected(old('classroom_id', $student->classroom_id) == $c->id)>
                                    {{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('classroom_id')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Additional information</label>
                    <textarea id="description" name="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Enter any additional information about the student">{{ old('description', $student->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary">{{ $student->exists ? 'Save changes' : 'Submit registration' }}</button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">View student list</a>
            </form>
        </div>
    </div>
@endsection