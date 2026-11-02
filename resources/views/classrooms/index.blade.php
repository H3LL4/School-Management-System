@extends('layouts.school')
@section('title', 'Classrooms')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Classrooms</h1>
        <a href="{{ route('classrooms.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add
            classroom</a>
    </div>

    <form method="GET" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-4"><input type="search" name="q" value="{{ request('q') }}" class="form-control"
                    placeholder="Search classroom name"></div>
            <div class="col-md-2">
                <select name="grade" class="form-select" onchange="this.form.submit()">
                    <option value="">All grades</option>
                    @for ($g = 1; $g <= 12; $g++)
                        <option value="{{ $g }}" @selected(request('grade') == $g)>Grade {{ $g }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <select name="teacher_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All teachers</option>
                    @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" @selected(request('teacher_id') == $t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a
                    href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary">Reset</a></div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Homeroom teacher</th>
                        <th>Students</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classrooms as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td class="fw-medium">{{ $c->name }}</td>
                            <td>Grade {{ $c->grade }}</td>
                            <td>{{ $c->teacher?->name ?? '—' }}</td>
                            <td><a href="{{ route('students.index', ['classroom_id' => $c->id]) }}">{{ $c->students_count }}</a>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('classrooms.edit', $c) }}" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('classrooms.destroy', $c) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this classroom?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No classrooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $classrooms->links() }}</div>
@endsection