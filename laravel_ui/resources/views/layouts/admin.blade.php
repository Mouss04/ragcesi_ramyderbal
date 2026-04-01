<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Witrack — Admin' }}</title>

    @php
        /* ── Dynamic theme color (set globally by admin) ── */
        $theme = $siteSetting->theme_color ?? '#0c7070';
        [$tr, $tg, $tb] = sscanf(ltrim($theme, '#'), "%02x%02x%02x");
        $tr = $tr ?? 12; $tg = $tg ?? 112; $tb = $tb ?? 112;
        $themeDark = sprintf('#%02x%02x%02x', (int)($tr*.60), (int)($tg*.60), (int)($tb*.60));
        $themeSoft = sprintf('#%02x%02x%02x',
            min(255, (int)(246 + $tr*.038)),
            min(255, (int)(246 + $tg*.038)),
            min(255, (int)(246 + $tb*.038))
        );
        $sidebarBg = sprintf('#%02x%02x%02x',
            max(5,  (int)($tr*.33)),
            max(12, (int)($tg*.43)),
            max(12, (int)($tb*.43))
        );
    /* lighter shades for gradients */
    $themeMid   = sprintf('#%02x%02x%02x', min(255,(int)($tr*1.4)), min(255,(int)($tg*1.4)), min(255,(int)($tb*1.4)));
    $themeLight = sprintf('#%02x%02x%02x', min(255,(int)($tr*1.85)), min(255,(int)($tg*1.85)), min(255,(int)($tb*1.85)));
    @endphp
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --teal:        {{ $theme }};
            --teal-dark:   {{ $themeDark }};
            --teal-soft:   {{ $themeSoft }};
            --teal-mid:    {{ $themeMid }};
            --teal-light:  {{ $themeLight }};
            --teal-shadow: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.22);
            --text:      #1e2c2c;
            --muted:     #6b8080;
            --line:      #dde8e8;
            --bg:        #f4f7f7;
            --white:     #ffffff;
            --sidebar-w: 240px;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: var(--bg);
            min-height: 100vh;
            display: flex;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: #0f3535;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow: hidden;
        }

        /* decorative glow behind logo */
        .sidebar::before {
            content: '';
            position: absolute;
            top: -60px; left: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(20,168,168,0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.4rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: relative;
        }

        /* invert the dark parts of the logo to white */
        .sidebar-logo img { filter: brightness(0) invert(1); }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
            flex: 1;
            overflow-y: auto;
        }
        .sidebar-nav::-webkit-scrollbar { width: 0; }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.28);
            padding: 0.9rem 0.85rem 0.3rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.62rem 0.9rem;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgba(255,255,255,0.55);
            transition: background 150ms ease, color 150ms ease;
            position: relative;
        }

        .nav-item svg { flex-shrink: 0; transition: opacity 150ms; }

        .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.95);
        }

        .nav-item.active {
            background: linear-gradient(90deg, rgba(20,168,168,0.35), rgba(20,168,168,0.12));
            color: #fff;
            font-weight: 600;
        }
        /* left accent bar on active */
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: #14a8a8;
        }

        /* sidebar bottom user strip */
        .sidebar-footer {
            padding: 0.9rem 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.6rem 0.8rem;
            border-radius: 10px;
            background: rgba(255,255,255,0.06);
        }
        .sidebar-user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: rgba(20,168,168,0.35);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }
        .sidebar-user-name { font-size: 0.8rem; font-weight: 700; color: rgba(255,255,255,0.85); }
        .sidebar-user-role { font-size: 0.7rem; color: rgba(255,255,255,0.4); }

        /* ── Main wrapper ── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Top bar ── */
        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0.9rem 1.6rem;
            background: var(--white);
            border-bottom: 1px solid var(--line);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--line);
            display: grid;
            place-items: center;
            flex-shrink: 0;
            overflow: hidden;
        }
        .avatar svg { opacity: 0.45; }

        .user-meta { text-align: right; line-height: 1.3; }
        .user-name  { display: block; font-weight: 700; font-size: 0.9rem; }
        .user-role  { display: block; font-size: 0.78rem; color: var(--muted); }

        /* ── Content area ── */
        .main-content {
            flex: 1;
            padding: 1.5rem 1.8rem;
        }

        /* ── Flash messages ── */
        .flash-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            border-radius: 10px;
            padding: 0.7rem 1rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .flash-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 10px;
            padding: 0.7rem 1rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        /* ── Shared components ── */
        .panel {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 1.4rem 1.5rem;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 1.2rem;
        }

        .grid { display: grid; gap: 1rem; }
        .cols-2 { grid-template-columns: repeat(2, 1fr); }
        .cols-3 { grid-template-columns: repeat(3, 1fr); }

        .card {
            background: var(--white);
            border: 1.5px solid var(--line);
            border-radius: 14px;
            padding: 1rem 1.2rem;
        }

        .card-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .card-metric {
            font-size: 2rem;
            font-weight: 800;
            color: var(--teal);
            line-height: 1;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: 9px;
            font: inherit;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: 140ms ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { background: var(--teal-dark); }
        .btn-outline {
            border-color: var(--line);
            color: var(--text);
            background: #fff;
        }
        .btn-danger { background: #dc2626; color: #fff; }

        label { display: block; margin-bottom: 0.3rem; font-weight: 600; font-size: 0.88rem; }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 9px;
            padding: 0.55rem 0.75rem;
            font: inherit;
            font-size: 0.9rem;
            color: var(--text);
            background: #fff;
            outline: none;
            transition: border-color 140ms;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--teal); }
        textarea { min-height: 110px; resize: vertical; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }
        th, td {
            text-align: left;
            padding: 0.65rem 0.8rem;
            border-bottom: 1px solid var(--line);
        }
        th { background: #f6fafa; font-weight: 700; font-size: 0.82rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
        tr:last-child td { border-bottom: none; }

        .pill {
            display: inline-block;
            padding: 0.15rem 0.55rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            background: var(--teal-soft);
            color: var(--teal-dark);
        }
        .pill.admin { background: #fef3c7; color: #92400e; }

        @media (max-width: 840px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
            .cols-2, .cols-3 { grid-template-columns: 1fr; }
        }
    </style>

    {{-- Dynamic theme overrides --}}
    <style>
        .sidebar {
            background: {{ $sidebarBg }};
        }
        .sidebar::before {
            background: radial-gradient(circle, rgba({{ $tr }},{{ $tg }},{{ $tb }},0.18) 0%, transparent 70%);
        }
        .nav-item.active {
            background: linear-gradient(90deg,
                rgba({{ $tr }},{{ $tg }},{{ $tb }},0.35),
                rgba({{ $tr }},{{ $tg }},{{ $tb }},0.10)) !important;
        }
        .nav-item.active::before {
            background: {{ $theme }};
        }
        .nav-item:hover {
            background: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.12);
        }
        .sidebar-user-avatar {
            background: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.35);
        }
        .topbar-company-dot {
            background: {{ $theme }};
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Sidebar ──────────────────────────────── --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        @if($siteSetting->company_logo)
            <img src="{{ Storage::url($siteSetting->company_logo) }}" alt="{{ $siteSetting->company_name ?? 'Logo' }}"
                 style="max-height:46px;max-width:150px;object-fit:contain;display:block;">
        @else
            <img src="{{ asset('logo_witrack.png') }}" alt="{{ $siteSetting->company_name ?? 'Witrack' }}"
                 style="width:100%;max-width:150px;height:auto;display:block;filter:brightness(0) invert(1);">
        @endif
    </div>

    <nav class="sidebar-nav">

        {{-- Main --}}
        <span class="nav-section-label">{{ __('Main') }}</span>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            {{ __('Home') }}
        </a>

        <a href="{{ route('employee.dashboard') }}"
           class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
            {{ __('AI Assistant') }}
        </a>

        {{-- Management --}}
        <span class="nav-section-label" style="margin-top:0.4rem;">{{ __('Management') }}</span>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
            {{ __('Users') }}
        </a>

        <a href="{{ route('admin.documents.index') }}"
           class="nav-item {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            {{ __('Documents') }}
        </a>

        {{-- Analytics --}}
        <span class="nav-section-label" style="margin-top:0.4rem;">{{ __('Analytics') }}</span>

        <a href="{{ route('employee.history') }}" class="nav-item {{ request()->routeIs('employee.history') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            {{ __('History') }}
        </a>

        <a href="#" class="nav-item">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            {{ __('Statistics') }}
        </a>

        {{-- Config --}}
        <span class="nav-section-label" style="margin-top:0.4rem;">{{ __('Configuration') }}</span>

        <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            {{ __('Settings') }}
        </a>

    </nav>

    {{-- Bottom user strip --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar" style="overflow:hidden;">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:.72rem;font-weight:800;color:rgba(255,255,255,.85);">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</span>
                @endif
            </div>
            <div style="overflow:hidden;">
                <div class="sidebar-user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">{{ ucfirst(__(auth()->user()->role === 'user' ? 'Employee' : ucfirst(auth()->user()->role))) }}</div>
            </div>
        </div>
    </div>
</aside>

{{-- ── Main ──────────────────────────────────── --}}
<div class="main-wrapper">
    <header class="topbar" style="justify-content:space-between;">

        <span style="font-size:.78rem;color:#9bb0b0;font-weight:500;">
            {{ __('Powered by') }} <strong style="color:var(--teal);font-weight:700;">Witrack</strong>
        </span>

        <div class="user-chip">

            <div class="user-meta">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role">{{ ucfirst(__(auth()->user()->role === 'user' ? 'Employee' : ucfirst(auth()->user()->role))) }}</span>
            </div>
            <div class="avatar" style="border:2px solid var(--line);">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:.82rem;font-weight:800;color:var(--teal);">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.85rem;border-radius:9px;border:1px solid var(--line);background:#fff;font:inherit;font-size:0.84rem;font-weight:600;color:var(--text);cursor:pointer;transition:140ms ease;" onmouseover="this.style.borderColor='#dc2626';this.style.color='#dc2626';" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--text)';">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </header>

    <main class="main-content">
        <div>
            @if (session('status'))
                <div class="flash-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash-error">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
