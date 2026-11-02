@extends('layouts.school')
@section('title', $classroom->exists ? 'Edit classroom' : 'New classroom')
@section('content')
    <h1 class="h3 mb-3">{{ $classroom->exists ? 'Edit classroom' : 'New classroom' }}</h1>
    <div class="card" style="max-width:640px">
        <div class="card-body p-4">
            <form method="POST"
                action="{{ $classroom->exists ? route('classrooms.update', $classroom) : route('classrooms.store') }}">
                @csrf
                @if ($classroom->exists) @method('PUT') @endif
                <x-field name="name" label="Classroom name (e.g. Grade 10 A)" :value="$classroom->name" required
                    autofocus />
                <div class="mb-3">
                    <label for="grade" class="form-label">Grade</label>
                    <select id="grade" name="grade" class="form-select @error('grade') is-invalid @enderror" required>
                        <option value="">Select grade…</option>
                        @for ($g = 1; $g <= 12; $g++)
                            <option value="{{ $g }}" @selected(old('grade', $classroom->grade) == $g)>Grade {{ $g }}</option>
                        @endfor
                    </select>
                    @error('grade')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Homeroom teacher</label>
                    <select id="teacher_id" name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                        <option value="">— None —</option>
                        @foreach ($teachers as $t)
                            <option value="{{ $t->id }}" @selected(old('teacher_id', $classroom->teacher_id) == $t->id)>
                                {{ $t->name }} ({{ $t->subject }})</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection