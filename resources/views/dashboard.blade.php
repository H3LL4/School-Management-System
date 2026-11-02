@extends('layouts.school')
@section('title', 'Dashboard')
@section('content')
    @php
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $max = max(1, $classrooms->max('students_count'));
    @endphp

    <style>
        @keyframes drift {
            from {
                transform: translate(0, 0) rotate(0)
            }

            to {
                transform: translate(-18px, 14px) rotate(12deg)
            }
        }

        /* ---- hero ---- */
        .dash-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1.25rem;
            padding: 2rem 2.25rem;
            color: #fff;
            margin-bottom: 1.5rem;
            background: linear-gradient(120deg, #101a33 0%, #1d3a9e 55%, #2f55e0 100%);
            box-shadow: 0 24px 44px -26px rgba(47, 85, 224, .7)
        }

        .dash-hero>*:not(.dash-shape) {
            position: relative;
            z-index: 1
        }

        .dash-hero h1 {
            font-size: clamp(1.6rem, 2.6vw, 2.3rem);
            margin: 0 0 .4rem;
            color: #fff
        }

        .dash-hero p {
            margin: 0 0 1.4rem;
            color: #c7d2f5;
            max-width: 52ch
        }

        .dash-date {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .8rem;
            background: rgba(255, 255, 255, .12);
            padding: .3rem .75rem;
            border-radius: 99px;
            margin-bottom: .9rem;
            color: #e6ebff
        }

        .dash-shape {
            position: absolute;
            pointer-events: none;
            animation: drift 14s ease-in-out infinite alternate
        }

        .dash-shape.a {
            width: 220px;
            height: 220px;
            border-radius: 32%;
            background: rgba(255, 255, 255, .08);
            right: -50px;
            top: -70px
        }

        .dash-shape.b {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: var(--sun);
            opacity: .92;
            right: 16%;
            bottom: -42px;
            animation-duration: 19s
        }

        .dash-shape.c {
            width: 260px;
            height: 260px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .14);
            right: 6%;
            top: -90px;
            animation-duration: 23s
        }

        .btn-sun {
            background: var(--sun);
            border-color: var(--sun);
            color: #101a33;
            font-weight: 600
        }

        .btn-sun:hover {
            background: #ffd75e;
            border-color: #ffd75e;
            color: #101a33;
            transform: translateY(-1px)
        }

        .btn-ghost {
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .28);
            color: #fff
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, .22);
            color: #fff
        }

        /* ---- stat cards ---- */
        .dash-in {
            animation: enter .55s cubic-bezier(.2, .7, .2, 1) both;
            animation-delay: calc(var(--i, 0) * 70ms)
        }

        .dash-stat {
            position: relative;
            display: block;
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 1.1rem;
            padding: 1.15rem 1.25rem;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 1px 2px rgba(16, 26, 51, .04), 0 10px 28px -16px rgba(16, 26, 51, .12);
            transition: transform .2s, box-shadow .2s
        }

        .dash-stat:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 34px -18px rgba(16, 26, 51, .35);
            color: inherit
        }

        .dash-stat::after {
            content: '';
            position: absolute;
            right: -34px;
            top: -34px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--tint);
            opacity: .7
        }

        .dash-stat>* {
            position: relative;
            z-index: 1
        }

        .dash-ico {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            color: #fff;
            background: var(--grad);
            box-shadow: 0 10px 18px -10px var(--c)
        }

        .dash-num {
            font-family: var(--font-display);
            font-size: 2.4rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.03em;
            margin-top: .95rem
        }

        .dash-label {
            color: var(--muted);
            font-size: .88rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: .4rem
        }

        .dash-label i {
            transition: transform .2s
        }

        .dash-stat:hover .dash-label i {
            transform: translateX(4px)
        }

        /* ---- panels ---- */
        .dash-student {
            transition: background-color .15s
        }

        .dash-student:hover {
            background: #f6f8fd
        }

        .dash-room {
            display: block;
            text-decoration: none;
            color: inherit
        }

        .dash-room .bar {
            height: 10px
        }
    </style>

    {{-- hero --}}
    <section class="dash-hero dash-in">
        <span class="dash-shape a"></span><span class="dash-shape b"></span><span class="dash-shape c"></span>
        <div class="dash-date"><i class="bi bi-calendar3"></i> {{ now()->format('l, F j') }}</div>
        <h1>{{ $greeting }}, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }} 👋</h1>
        <p>{{ $studentCount }} students in {{ $classroomCount }} classrooms, taught by {{ $teacherCount }} teachers. Here is
            your school at a glance.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('students.create') }}" class="btn btn-sun"><i class="bi bi-person-plus me-1"></i>Register
                student</a>
            <a href="{{ route('enrollments.create') }}" class="btn btn-ghost"><i class="bi bi-card-checklist me-1"></i>New
                enrollment</a>
            <a href="{{ route('payments.create') }}" class="btn btn-ghost"><i class="bi bi-cash-coin me-1"></i>Record
                payment</a>
        </div>
    </section>

    {{-- stats --}}
    <div class="row g-3 mb-4">
        @foreach ([
                ['Students', $studentCount, 'bi-people-fill', 'students.index', '#2f55e0', 'linear-gradient(135deg,#5a7bf2,#2f55e0)', '#e6ecff'],
                ['Teachers', $teacherCount, 'bi-person-workspace', 'teachers.index', '#1f9d6b', 'linear-gradient(135deg,#3fc08e,#1f9d6b)', '#dff5ea'],
                ['Classrooms', $classroomCount, 'bi-door-open-fill', 'classrooms.index', '#e59a00', 'linear-gradient(135deg,#ffc933,#e59a00)', '#fff3cf'],
                ['Courses', $courseCount, 'bi-journal-bookmark-fill', 'courses.index', '#7a4df0', 'linear-gradient(135deg,#9a76ff,#7a4df0)', '#ece4ff'],
            ] as $i => [$label, $value, $icon, $route, $color, $grad, $tint])
            <div class="col-6 col-xl-3">
                <a href="{{ route($route) }}" class="dash-stat dash-in"
                    style="--c:{{ $color }};--grad:{{ $grad }};--tint:{{ $tint }};--i:{{ $i + 1 }}">
                    <div class="dash-ico"><i class="bi {{ $icon }}"></i></div>
                    <div class="dash-num" data-count="{{ $value }}">{{ $value }}</div>
                    <div class="dash-label"><span>{{ $label }}</span><i class="bi bi-arrow-right"></i></div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- panels --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100 dash-in" style="--i:5">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Newest students</span><a href="{{ route('students.index') }}"
                        class="small">View all</a>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($recentStudents as $s)
                        <li class="list-group-item dash-student d-flex align-items-center gap-3 py-3">
                            <span class="avatar"
                                style="--h: {{ crc32($s->name) % 360 }}">{{ mb_strtoupper(mb_substr($s->name, 0, 1)) }}</span>
                            <div class="flex-fill">
                                <div class="fw-medium">{{ $s->name }}</div>
                                <div class="small text-muted">{{ $s->email }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge text-bg-light border">{{ $s->classroom->name }}</span>
                                <div class="small text-muted mt-1">{{ $s->created_at->diffForHumans() }}</div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-5">No students yet. Register the first one.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100 dash-in" style="--i:6">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Students per classroom</span><a href="{{ route('classrooms.index') }}"
                        class="small">Manage</a>
                </div>
                <div class="card-body d-grid gap-3 align-content-start">
                    @forelse ($classrooms as $c)
                        <a href="{{ route('students.index', ['classroom_id' => $c->id]) }}" class="dash-room">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-medium">{{ $c->name }}</span>
                                <span class="text-muted">{{ $c->students_count }}
                                    {{ \Illuminate\Support\Str::plural('student', $c->students_count) }}</span>
                            </div>
                            <div class="bar"><i
                                    style="--w: {{ round($c->students_count / $max * 100) }}%; background: hsl({{ crc32($c->name) % 360 }} 70% 52%)"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-muted text-center py-4">No classrooms yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection