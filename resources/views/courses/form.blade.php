@extends('layouts.school')
@section('title', $course->exists ? 'Edit course' : 'New course')
@section('content')
<h1 class="h3 mb-3">{{ $course->exists ? 'Edit course' : 'New course' }}</h1>
<div class="card" style="max-width:760px"><div class="card-body p-4">
    <form method="POST" action="{{ $course->exists ? route('courses.update', $course) : route('courses.store') }}">
        @csrf
        @if ($course->exists) @method('PUT') @endif
        <div class="row">
            <x-field class="col-md-4" name="code" label="Course code" :value="$course->code" required autofocus />
            <x-field class="col-md-8" name="title" label="Title" :value="$course->title" required />
            <x-select class="col-md-6" name="department_id" label="Department" :options="$departmentOptions" :selected="$course->department_id" required />
            <x-select class="col-md-6" name="teacher_id" label="Instructor (optional)" :options="$teacherOptions" :selected="$course->teacher_id" placeholder="— None —" />
            <x-field class="col-md-4" name="credit_hours" label="Credit hours" type="number" :value="$course->credit_hours" min="1" max="10" required />
            <x-field class="col-md-4" name="capacity" label="Capacity (seats)" type="number" :value="$course->capacity" min="1" max="500" required />
            <x-field class="col-md-4" name="tuition_fee" label="Tuition fee ($)" type="number" :value="$course->tuition_fee" step="0.01" min="0" required />
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div></div>
@endsection
