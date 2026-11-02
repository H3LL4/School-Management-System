@extends('layouts.school')
@section('title', $enrollment->exists ? 'Edit enrollment' : 'New enrollment')
@section('content')
<h1 class="h3 mb-3">{{ $enrollment->exists ? 'Edit enrollment' : 'New enrollment' }}</h1>
<div class="card" style="max-width:700px"><div class="card-body p-4">
    <form method="POST" action="{{ $enrollment->exists ? route('enrollments.update', $enrollment) : route('enrollments.store') }}">
        @csrf
        @if ($enrollment->exists) @method('PUT') @endif

        @if ($enrollment->exists)
            <dl class="row mb-3">
                <dt class="col-sm-3">Student</dt><dd class="col-sm-9">{{ $enrollment->student->name }}</dd>
                <dt class="col-sm-3">Course</dt><dd class="col-sm-9">{{ $enrollment->course->code }} · {{ $enrollment->course->title }}</dd>
                <dt class="col-sm-3">Semester</dt><dd class="col-sm-9">{{ $enrollment->semester->name }}</dd>
            </dl>
            <p class="text-muted small">Only the status can be changed. To enrol someone in a different course or semester, create a new enrollment.</p>
        @else
            <x-select name="student_id" label="Student" :options="$studentOptions" :selected="$enrollment->student_id" required />
            <x-select name="course_id" label="Course" :options="$courseOptions" :selected="$enrollment->course_id" required />
            <x-select name="semester_id" label="Semester" :options="$semesterOptions" :selected="$enrollment->semester_id" required />
        @endif

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach (\App\Models\Enrollment::STATUSES as $st)
                    <option @selected(old('status', $enrollment->status) === $st)>{{ $st }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Enrolled is blocked when the course is full. Waitlisted always works.</div>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div></div>
@endsection
