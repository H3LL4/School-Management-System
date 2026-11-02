<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SchoolDesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #101a33;
            --brand: #2f55e0;
            --sun: #ffc933;
            --line: #e3e8f0
        }

        body {
            margin: 0;
            font-family: 'DM Sans', system-ui, sans-serif;
            color: #1b2336;
            background: #eef1f6
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr)
        }

        .auth-art {
            position: relative;
            overflow: hidden;
            background: var(--ink);
            color: #e8ecf8;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between
        }

        .auth-art>*:not(.shape) {
            position: relative;
            z-index: 1
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: .7rem;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 700;
            font-size: 1.35rem
        }

        .auth-logo {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--sun);
            color: var(--ink);
            display: grid;
            place-items: center;
            font-size: 1.1rem
        }

        .auth-art h2 {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: clamp(2rem, 3.4vw, 3.1rem);
            line-height: 1.08;
            font-weight: 700;
            max-width: 14ch;
            margin: 0 0 1rem;
            letter-spacing: -.02em
        }

        .auth-art p {
            color: #aab5d3;
            max-width: 38ch;
            margin: 0;
            line-height: 1.6
        }

        .shape {
            position: absolute;
            animation: drift 14s ease-in-out infinite alternate
        }

        .shape.a {
            width: 230px;
            height: 230px;
            border-radius: 30%;
            background: var(--brand);
            right: -70px;
            top: 12%
        }

        .shape.b {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--sun);
            right: 20%;
            bottom: 14%;
            animation-duration: 19s
        }

        .shape.c {
            width: 320px;
            height: 320px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .12);
            left: -100px;
            bottom: -120px;
            animation-duration: 23s
        }

        @keyframes drift {
            from {
                transform: translate(0, 0) rotate(0)
            }

            to {
                transform: translate(-22px, 18px) rotate(14deg)
            }
        }

        .auth-main {
            display: grid;
            place-items: center;
            padding: 2rem
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 24px 50px -24px rgba(16, 26, 51, .25);
            animation: rise .6s cubic-bezier(.2, .7, .2, 1) both
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(16px)
            }
        }

        .auth-mobile-brand {
            display: none;
            align-items: center;
            gap: .6rem;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 1.2rem
        }

        .auth-switch {
            text-align: center;
            font-size: .9rem;
            color: #6a7590;
            margin: 1.25rem 0 0
        }

        .auth-card a {
            color: var(--brand)
        }

        .auth-card input[type=email],
        .auth-card input[type=password],
        .auth-card input[type=text] {
            border-radius: .7rem;
            border-color: #d5dbe7;
            padding: .65rem .85rem;
            transition: border-color .2s, box-shadow .2s
        }

        .auth-card input:focus {
            border-color: var(--brand) !important;
            box-shadow: 0 0 0 4px rgba(47, 85, 224, .14) !important;
            outline: none
        }

        .auth-card input[type=checkbox] {
            accent-color: var(--brand)
        }

        .auth-card button.bg-gray-800 {
            background: var(--brand) !important;
            border-radius: .7rem;
            padding: .65rem 1.2rem;
            text-transform: none;
            letter-spacing: 0;
            font-size: .9rem;
            transition: transform .15s, box-shadow .2s, background-color .2s
        }

        .auth-card button.bg-gray-800:hover {
            background: #2444bb !important;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px -8px rgba(47, 85, 224, .7)
        }

        .auth-card button.bg-gray-800:active {
            transform: scale(.97)
        }

        @media (max-width:900px) {
            .auth-shell {
                grid-template-columns: 1fr
            }

            .auth-art {
                display: none
            }

            .auth-mobile-brand {
                display: flex
            }
        }

        @media (prefers-reduced-motion:reduce) {
            * {
                animation: none !important;
                transition: none !important
            }
        }
    </style>
</head>

<body>
    <div class="auth-shell">
        <aside class="auth-art">
            <div class="auth-brand"><span class="auth-logo">&#127891;</span> SchoolDesk</div>
            <div>
                <h2>Every student, class and teacher in one place.</h2>
                <p>Sign in to manage enrolment, classrooms and staff, with search and filters that keep up with you.</p>
            </div>
            <small style="color:#7f8ab0">School Management System</small>
            <span class="shape a"></span><span class="shape b"></span><span class="shape c"></span>
        </aside>

        <main class="auth-main">
            <div style="width:100%;max-width:420px">
                <div class="auth-mobile-brand"><span class="auth-logo">&#127891;</span> SchoolDesk</div>
                <div class="auth-card">
                    {{ $slot }}
                </div>
                @if (request()->routeIs('login'))
                    <p class="auth-switch">New here? <a href="{{ route('register') }}" style="color:var(--brand)">Create an
                            account</a></p>
                @endif
            </div>
        </main>
    </div>
</body>

</html>