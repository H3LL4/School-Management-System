<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'School') · SchoolDesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root{
            --ink:#101a33; --brand:#2f55e0; --sun:#ffc933;
            --paper:#eef1f6; --line:#e3e8f0; --text:#1b2336; --muted:#6a7590;
            --ok:#1f9d6b; --bad:#d9433f;
            --font-body:'DM Sans',system-ui,sans-serif;
            --font-display:'Bricolage Grotesque','DM Sans',sans-serif;
            --bs-body-font-family:var(--font-body); --bs-body-bg:var(--paper); --bs-body-color:var(--text);
            --bs-primary:#2f55e0; --bs-primary-rgb:47,85,224;
            --bs-link-color:#2f55e0; --bs-link-hover-color:#2444bb;
            --bs-border-color:var(--line);
        }
        @view-transition{navigation:auto}

        h1,h2,h3,h4,h5,.h1,.h2,.h3,.h4,.h5{font-family:var(--font-display);letter-spacing:-.02em}
        .text-muted{color:var(--muted)!important}

        /* ---------- app shell ---------- */
        .side{position:fixed;inset:0 auto 0 0;width:248px;background:var(--ink);color:#c5cee6;padding:1.25rem .9rem;display:flex;flex-direction:column;z-index:1040;view-transition-name:sidebar;transition:transform .3s cubic-bezier(.2,.7,.2,1)}
        .main{margin-left:248px;min-width:0}
        .page{padding:2rem clamp(1.5rem,3vw,3.5rem);max-width:none;animation:enter .5s cubic-bezier(.2,.7,.2,1) both}
        @keyframes enter{from{opacity:0;transform:translateY(10px)}}
        .brand{display:flex;align-items:center;gap:.7rem;color:#fff;text-decoration:none;font-family:var(--font-display);font-weight:700;font-size:1.3rem;padding:.25rem .6rem 1.5rem}
        .brand:hover{color:#fff}
        .brand-mark{width:38px;height:38px;border-radius:12px;background:var(--sun);color:var(--ink);display:grid;place-items:center;transition:transform .3s}
        .brand:hover .brand-mark{transform:rotate(-8deg) scale(1.06)}
        .side-nav{display:grid;gap:.25rem}
        .side-link{position:relative;display:flex;align-items:center;gap:.8rem;padding:.65rem .8rem;border-radius:.75rem;color:#aab5d3;text-decoration:none;font-weight:500;transition:background-color .2s,color .2s}
        .side-link i{font-size:1.1rem;transition:transform .2s}
        .side-link:hover{background:rgba(255,255,255,.06);color:#fff}
        .side-link:hover i{transform:scale(1.12)}
        .side-link.active{background:rgba(255,255,255,.1);color:#fff}
        .side-link.active::before{content:'';position:absolute;left:-.9rem;top:22%;bottom:22%;width:4px;border-radius:0 4px 4px 0;background:var(--sun)}
        .side-user{margin-top:auto;display:flex;align-items:center;gap:.5rem;padding:.7rem;border-radius:.9rem;background:rgba(255,255,255,.06)}
        .user-chip{display:flex;align-items:center;gap:.65rem;min-width:0;flex:1;color:#fff;text-decoration:none}
        .user-chip:hover{color:#fff}
        .user-meta{display:grid;line-height:1.2;min-width:0}
        .user-meta strong{font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .user-meta small{color:#8f9bbd}
        .logout{border:0;background:transparent;color:#aab5d3;width:36px;height:36px;border-radius:.6rem;transition:background-color .2s,color .2s}
        .logout:hover{background:rgba(255,255,255,.12);color:#fff}
        .mobbar,.backdrop{display:none}
        @media (max-width:991.98px){
            .side{transform:translateX(-100%)}
            .side.open{transform:none}
            .main{margin-left:0}
            .page{padding:1.25rem 1rem}
            .mobbar{display:flex;align-items:center;gap:.8rem;padding:.7rem 1rem;background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:1020;font-family:var(--font-display)}
            .backdrop{display:block;position:fixed;inset:0;background:rgba(10,16,32,.5);opacity:0;pointer-events:none;transition:opacity .3s;z-index:1030}
            .backdrop.show{opacity:1;pointer-events:auto}
        }

        /* ---------- components ---------- */
        .card{border:1px solid var(--line);border-radius:1rem;box-shadow:0 1px 2px rgba(16,26,51,.04),0 10px 28px -16px rgba(16,26,51,.12)}
        .card-header{border-bottom:1px solid var(--line);padding:.9rem 1.1rem}
        .btn{border-radius:.7rem;font-weight:500;transition:transform .15s,box-shadow .2s,background-color .2s,color .2s}
        .btn:active{transform:scale(.97)}
        .btn-primary{--bs-btn-bg:#2f55e0;--bs-btn-border-color:#2f55e0;--bs-btn-hover-bg:#2444bb;--bs-btn-hover-border-color:#2444bb;--bs-btn-active-bg:#2444bb;--bs-btn-active-border-color:#2444bb;--bs-btn-disabled-bg:#2f55e0;--bs-btn-disabled-border-color:#2f55e0}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 8px 18px -8px rgba(47,85,224,.7)}
        .btn-outline-primary{--bs-btn-color:#2f55e0;--bs-btn-border-color:#2f55e0;--bs-btn-hover-bg:#2f55e0;--bs-btn-hover-border-color:#2f55e0;--bs-btn-active-bg:#2f55e0;--bs-btn-active-border-color:#2f55e0}
        .form-control,.form-select{border-radius:.7rem;border-color:#d5dbe7;padding:.6rem .85rem;transition:border-color .2s,box-shadow .2s}
        .form-control:focus,.form-select:focus{border-color:var(--brand);box-shadow:0 0 0 4px rgba(47,85,224,.14)}
        .form-label{font-weight:500;font-size:.9rem}
        .table>:not(caption)>*>*{padding:.85rem 1rem;border-color:var(--line)}
        .table thead th{font-size:.8rem;color:var(--muted);font-weight:600;background:#f8f9fc}
        .table tbody tr{transition:background-color .15s}
        .table tbody tr:hover{background:#f6f8fd}
        .table .btn-sm{border:0;width:34px;height:34px;display:inline-grid;place-items:center;padding:0;border-radius:.6rem}
        .table .btn-outline-primary{background:#eaf0ff;color:var(--brand)}
        .table .btn-outline-primary:hover{background:var(--brand);color:#fff}
        .table .btn-outline-danger{background:#ffeceb;color:var(--bad)}
        .table .btn-outline-danger:hover{background:var(--bad);color:#fff}
        .table .btn-outline-secondary{background:#eef1f6;color:#4a5570}
        .table .btn-outline-secondary:hover{background:#4a5570;color:#fff}
        .badge{font-weight:500}
        .page-link{border:0;border-radius:.5rem;margin:0 2px;color:var(--brand)}
        .page-item.active .page-link{background:var(--brand);color:#fff}
        .avatar{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;flex:none;font-weight:600;font-size:.9rem;color:hsl(var(--h) 55% 28%);background:hsl(var(--h) 80% 90%)}
        .avatar.sm{width:34px;height:34px;font-size:.8rem}
        .stat-card{flex-direction:row;align-items:center;gap:1rem;padding:1.15rem 1.25rem;transition:transform .2s,box-shadow .2s}
        .stat-card:hover{transform:translateY(-3px);box-shadow:0 14px 30px -16px rgba(16,26,51,.25)}
        .stat-icon{width:52px;height:52px;border-radius:16px;display:grid;place-items:center;font-size:1.4rem;flex:none}
        .tone-blue .stat-icon{background:#e6ecff;color:var(--brand)}
        .tone-amber .stat-icon{background:#fff3cf;color:#a87500}
        .tone-green .stat-icon{background:#dff5ea;color:var(--ok)}
        .stat{font-family:var(--font-display);font-size:2.1rem;font-weight:700;line-height:1;letter-spacing:-.02em}
        .bar{height:8px;border-radius:99px;background:#e9edf6;overflow:hidden;min-width:90px}
        .bar>i{display:block;height:100%;width:var(--w);border-radius:inherit;background:var(--brand);transform-origin:left;animation:grow .9s .25s cubic-bezier(.2,.7,.2,1) both}
        @keyframes grow{from{transform:scaleX(0)}}

        /* spinner on submit */
        .btn.is-loading{position:relative;pointer-events:none;color:transparent!important}
        .btn.is-loading::after{content:'';position:absolute;inset:0;margin:auto;width:1em;height:1em;border:2px solid #fff;border-right-color:transparent;border-radius:50%;animation:spin .6s linear infinite}
        .btn[class*="btn-outline"].is-loading::after,.btn-light.is-loading::after{border-color:var(--brand);border-right-color:transparent}
        @keyframes spin{to{transform:rotate(360deg)}}

        /* toasts */
        .toast-stack{position:fixed;top:1rem;right:1rem;z-index:2000;display:grid;gap:.6rem;width:min(380px,calc(100vw - 2rem))}
        .toast-card{position:relative;overflow:hidden;display:flex;align-items:center;gap:.7rem;background:#fff;border:1px solid var(--line);border-left:4px solid var(--ok);border-radius:.9rem;padding:.8rem .9rem;box-shadow:0 16px 36px -14px rgba(16,26,51,.3);animation:toast-in .4s cubic-bezier(.2,.8,.2,1) both}
        .toast-card.err{border-left-color:var(--bad)}
        .toast-card .t-ico{font-size:1.2rem;color:var(--ok)}
        .toast-card.err .t-ico{color:var(--bad)}
        .toast-card span{flex:1;font-size:.92rem}
        .toast-x{border:0;background:transparent;font-size:1.3rem;line-height:1;color:var(--muted)}
        .toast-card::after{content:'';position:absolute;left:0;right:0;bottom:0;height:3px;background:var(--ok);opacity:.35;transform-origin:left;animation:toast-life 4.5s linear forwards}
        .toast-card.err::after{background:var(--bad)}
        .toast-card.out{animation:toast-out .3s ease forwards}
        @keyframes toast-in{from{opacity:0;transform:translateX(24px) scale(.97)}}
        @keyframes toast-out{to{opacity:0;transform:translateX(24px)}}
        @keyframes toast-life{to{transform:scaleX(0)}}

        /* confirm modal */
        .modal-content{border:0;border-radius:1.25rem;box-shadow:0 30px 60px -20px rgba(16,26,51,.45)}
        .modal.fade .modal-dialog{transform:scale(.94) translateY(8px);transition:transform .25s cubic-bezier(.2,.7,.2,1)}
        .modal.show .modal-dialog{transform:none}
        .confirm-icon{width:58px;height:58px;border-radius:50%;background:#ffeceb;color:var(--bad);display:grid;place-items:center;font-size:1.5rem;margin:0 auto 1rem}

        @media (prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
    </style>
</head>
<body>
<aside class="side" id="side">
    <a href="{{ route('dashboard') }}" class="brand"><span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span><span>SchoolDesk</span></a>

    <nav class="side-nav">
        @php
            $groups = [
                '' => [['dashboard', 'Dashboard', 'bi-grid-1x2-fill', 'dashboard']],
                'School' => [
                    ['students.index', 'Students', 'bi-people-fill', 'students.*'],
                    ['classrooms.index', 'Classrooms', 'bi-door-open-fill', 'classrooms.*'],
                    ['teachers.index', 'Teachers', 'bi-person-workspace', 'teachers.*'],
                ],
                'Academics' => [
                    ['departments.index', 'Departments', 'bi-diagram-3-fill', 'departments.*'],
                    ['courses.index', 'Courses', 'bi-journal-bookmark-fill', 'courses.*'],
                    ['semesters.index', 'Semesters', 'bi-calendar3', 'semesters.*'],
                    ['enrollments.index', 'Enrollments', 'bi-card-checklist', 'enrollments.*'],
                ],
                'Finance' => [
                    ['payments.index', 'Payments', 'bi-cash-coin', 'payments.*'],
                    ['reports.index', 'Reports', 'bi-bar-chart-line-fill', 'reports.*'],
                ],
            ];
            if (auth()->user()->isAdmin()) {
                $groups['Admin'] = [
                    ['security.index', 'Security & Access', 'bi-shield-lock-fill', 'security.*'],
                    ['activity.index', 'Activity Log', 'bi-clock-history', 'activity.*'],
                ];
            }
        @endphp
        @foreach ($groups as $title => $items)
            @if ($title)
            <div class="side-label">{{ $title }}</div>@endif
            @foreach ($items as [$route, $label, $icon, $match])
                <a href="{{ route($route) }}" class="side-link {{ request()->routeIs($match) ? 'active' : '' }}"><i
                        class="bi {{ $icon }}"></i><span>{{ $label }}</span></a>
            @endforeach
        @endforeach
    </nav>

    <div class="side-user">
        <a href="{{ route('profile.edit') }}" class="user-chip">
            <span class="avatar sm" style="--h: {{ crc32(auth()->user()->name) % 360 }}">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="user-meta"><strong>{{ auth()->user()->name }}</strong><small>View profile</small></span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout" title="Logout"><i class="bi bi-box-arrow-right"></i></button>
        </form>
    </div>
</aside>
<div class="backdrop" id="backdrop"></div>

<div class="main">
    <div class="mobbar">
        <button id="menuBtn" class="btn btn-light btn-sm" aria-label="Open menu"><i class="bi bi-list fs-5"></i></button>
        <strong>SchoolDesk</strong>
    </div>
    <main class="page">
        @yield('content')
    </main>
</div>

<div class="toast-stack">
    @if (session('success'))
        <div class="toast-card" role="status"><i class="bi bi-check-circle-fill t-ico"></i><span>{{ session('success') }}</span><button class="toast-x" aria-label="Close">&times;</button></div>
    @endif
    @if (session('error'))
        <div class="toast-card err" role="alert"><i class="bi bi-exclamation-circle-fill t-ico"></i><span>{{ session('error') }}</span><button class="toast-x" aria-label="Close">&times;</button></div>
    @endif
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div class="confirm-icon"><i class="bi bi-trash3"></i></div>
                <h5 class="mb-1" id="confirmText">Are you sure?</h5>
                <p class="text-muted mb-4">This action cannot be undone.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmOk">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    // mobile sidebar
    const side = document.getElementById('side'), back = document.getElementById('backdrop');
    const toggle = open => { side.classList.toggle('open', open); back.classList.toggle('show', open); };
    document.getElementById('menuBtn')?.addEventListener('click', () => toggle(true));
    back.addEventListener('click', () => toggle(false));

    // toasts close themselves
    document.querySelectorAll('.toast-card').forEach((t, i) => {
        const close = () => { t.classList.add('out'); setTimeout(() => t.remove(), 300); };
        t.querySelector('.toast-x')?.addEventListener('click', close);
        setTimeout(close, 4500 + i * 400);
    });

    // numbers count up
    if (!reduce) document.querySelectorAll('[data-count]').forEach(el => {
        const end = parseInt(el.dataset.count, 10) || 0, t0 = performance.now(), dur = 900;
        const tick = now => {
            const p = Math.min((now - t0) / dur, 1);
            el.textContent = Math.round(end * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(tick);
        };
        el.textContent = '0';
        requestAnimationFrame(tick);
    });

    // nicer delete confirmation (replaces the browser's confirm box)
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    let pending = null;
    document.querySelectorAll('form[onsubmit*="confirm("]').forEach(form => {
        const m = form.getAttribute('onsubmit').match(/confirm\('([^']*)'\)/);
        form.removeAttribute('onsubmit');
        form.addEventListener('submit', e => {
            if (form.dataset.ok) return;
            e.preventDefault();
            document.getElementById('confirmText').textContent = m ? m[1] : 'Are you sure?';
            pending = form;
            modal.show();
        });
    });
    document.getElementById('confirmOk').addEventListener('click', () => {
        if (!pending) return;
        pending.dataset.ok = '1';
        modal.hide();
        pending.requestSubmit();
    });

    // spinner on buttons while a form is sending
    document.querySelectorAll('form').forEach(f => f.addEventListener('submit', e => {
        if (e.defaultPrevented || (f.getAttribute('method') || 'get').toLowerCase() === 'get') return;
        f.querySelector('button:not([type=button])')?.classList.add('is-loading');
    }));
    addEventListener('pageshow', () => document.querySelectorAll('.is-loading').forEach(b => b.classList.remove('is-loading')));

    // live search: filter while typing, keep the cursor in the box
    document.querySelectorAll('form[method="GET"] input[type="search"]').forEach(inp => {
        let t;
        inp.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => { sessionStorage.setItem('refocus', inp.name); inp.form.requestSubmit(); }, 450);
        });
        if (sessionStorage.getItem('refocus') === inp.name) {
            sessionStorage.removeItem('refocus');
            inp.focus();
            inp.setSelectionRange(inp.value.length, inp.value.length);
        }
    });
})();
</script>
</body>
</html>