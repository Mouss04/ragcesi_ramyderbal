<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Witrack — Agent' }}</title>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --teal:        #0c7070;
            --teal-dark:   #074444;
            --teal-soft:   #f0f7f7;
            --teal-mid:    #14a8a8;
            --teal-light:  #1fd0d0;
            --teal-shadow: rgba(12,112,112,0.22);
            --sidebar-bg:  #071f1f;
            --text:      #1e2c2c;
            --muted:     #6b8080;
            --line:      #dde8e8;
            --bg:        #f4f7f7;
            --white:     #ffffff;
            --sidebar-w: 240px;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: var(--text);
            background: var(--bg);
            min-height: 100vh;
            display: flex;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow: hidden;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: -60px; left: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(12,112,112,0.20) 0%, transparent 70%);
            pointer-events: none;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.4rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            color: #fff;
        }

        .sidebar-badge-icon {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--teal), var(--teal-mid));
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .sidebar-badge-text {
            font-size: 0.9rem;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .sidebar-badge-sub {
            font-size: 0.65rem;
            font-weight: 600;
            color: rgba(255,255,255,0.35);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
            flex: 1;
            overflow-y: auto;
        }
        .sidebar-nav::-webkit-scrollbar { width: 0; }
        .sidebar-nav { scrollbar-width: none; }

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

        .nav-item svg { flex-shrink: 0; }

        .nav-item:hover {
            background: rgba(12,112,112,0.15);
            color: rgba(255,255,255,0.95);
        }

        .nav-item.active {
            background: linear-gradient(90deg, rgba(12,112,112,0.38), rgba(12,112,112,0.12));
            color: #fff;
            font-weight: 600;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--teal-mid);
        }

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
            background: rgba(12,112,112,0.4);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .sidebar-user-name { font-size: 0.8rem; font-weight: 700; color: rgba(255,255,255,0.85); }
        .sidebar-user-role { font-size: 0.68rem; color: var(--teal-mid); font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }

        /* ── Main ── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.9rem 1.6rem;
            background: var(--white);
            border-bottom: 1px solid var(--line);
        }

        .user-chip { display: flex; align-items: center; gap: 0.65rem; }
        .user-meta { text-align: right; line-height: 1.3; }
        .user-name  { display: block; font-weight: 700; font-size: 0.9rem; }
        .user-role  { display: block; font-size: 0.78rem; color: var(--teal); font-weight: 700; }

        .avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--teal-soft);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .main-content { flex: 1; padding: 1.5rem 1.8rem; }

        .flash-success {
            background: #ecfdf5; border: 1px solid #a7f3d0;
            color: #065f46; border-radius: 10px;
            padding: 0.7rem 1rem; margin-bottom: 1rem; font-weight: 600;
        }
        .flash-error {
            background: #fef2f2; border: 1px solid #fecaca;
            color: #991b1b; border-radius: 10px;
            padding: 0.7rem 1rem; margin-bottom: 1rem; font-weight: 600;
        }

        /* Shared components */
        .panel { background: var(--white); border: 1px solid var(--line); border-radius: 16px; padding: 1.4rem 1.5rem; }
        .page-title { font-size: 1.25rem; font-weight: 800; margin-bottom: 1.2rem; }

        .grid { display: grid; gap: 1rem; }
        .cols-2 { grid-template-columns: repeat(2, 1fr); }
        .cols-3 { grid-template-columns: repeat(3, 1fr); }

        .card { background: var(--white); border: 1.5px solid var(--line); border-radius: 14px; padding: 1rem 1.2rem; }
        .card-label { font-size: 0.82rem; font-weight: 600; color: var(--text); margin-bottom: 0.5rem; }
        .card-metric { font-size: 2rem; font-weight: 800; color: var(--teal); line-height: 1; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 0.4rem; padding: 0.55rem 1rem; border-radius: 9px;
            font: inherit; font-weight: 600; font-size: 0.88rem;
            cursor: pointer; text-decoration: none; border: 1px solid transparent;
            transition: 140ms ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { background: var(--teal-dark); }
        .btn-outline { border-color: var(--line); color: var(--text); background: #fff; }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-danger:hover { background: #b91c1c; }

        label { display: block; margin-bottom: 0.3rem; font-weight: 600; font-size: 0.88rem; }
        input, select, textarea {
            width: 100%; border: 1px solid var(--line); border-radius: 9px;
            padding: 0.55rem 0.75rem; font: inherit; font-size: 0.9rem;
            color: var(--text); background: #fff; outline: none; transition: border-color 140ms;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--teal); }

        table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        th, td { text-align: left; padding: 0.65rem 0.8rem; border-bottom: 1px solid var(--line); }
        th { background: #f6fafa; font-weight: 700; font-size: 0.82rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
        tr:last-child td { border-bottom: none; }

        .pill { display: inline-block; padding: 0.15rem 0.55rem; border-radius: 999px; font-size: 0.78rem; font-weight: 700; }
        .pill-teal { background: var(--teal-soft); color: var(--teal-dark); }
        .pill-amber { background: #fef3c7; color: #92400e; }
        .pill-red { background: #fee2e2; color: #991b1b; }

        @media (max-width: 840px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
            .cols-2, .cols-3 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── Sidebar ── --}}
<aside class="sidebar">
  <div class="sidebar-logo">
        <a href="{{ url('/') }}" class="nav-brand" style="display:flex;align-items:center;gap:.65rem;color:#fff;text-decoration:none;">
            <img src="{{ asset('bagroung_logo.png') }}" alt="Witrack logo" class="nav-brand-logo" style="height:38px;object-fit:contain;">
            <div class="nav-brand-text" style="display:flex;flex-direction:column;line-height:1.05;">
                <span class="nav-brand-name" style="font-weight:800;letter-spacing:.03em;">Witrack</span>
                <span class="nav-brand-sub" style="font-size:.65rem;font-weight:600;color:rgba(255,255,255,0.35);letter-spacing:.06em;text-transform:uppercase;">
                    Links the Dots
                </span>
            </div>
        </a>
    </div>
    <nav class="sidebar-nav">
        <span class="nav-section-label">Gestion globale</span>

        <a href="{{ route('witrack.dashboard') }}"
           class="nav-item {{ request()->routeIs('witrack.dashboard') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Entreprises
        </a>

        <a href="{{ route('witrack.companies.create') }}"
           class="nav-item {{ request()->routeIs('witrack.companies.create') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Nouvelle entreprise
        </a>

        <span class="nav-section-label" style="margin-top:.5rem;">Compte</span>

        <a href="{{ route('witrack.settings') }}"
           class="nav-item {{ request()->routeIs('witrack.settings') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
            Paramètres
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <span style="font-size:.72rem;font-weight:800;color:rgba(255,255,255,.85);">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
            </div>
            <div style="overflow:hidden;">
                <div class="sidebar-user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">Witrack Agent</div>
            </div>
        </div>
    </div>
</aside>

{{-- ── Main ── --}}
<div class="main-wrapper">
    <header class="topbar">
        <span style="font-size:.78rem;color:#9bb0b0;font-weight:500;">
            Tableau de bord <strong style="color:var(--teal);font-weight:700;">Agent Witrack</strong>
        </span>

        <div class="user-chip">
            <div class="user-meta">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role">Witrack Agent</span>
            </div>
            <div class="avatar">
                <span style="font-size:.82rem;font-weight:800;color:var(--teal);">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.85rem;border-radius:9px;border:1px solid var(--line);background:#fff;font:inherit;font-size:0.84rem;font-weight:600;color:var(--text);cursor:pointer;transition:140ms ease;"
                    onmouseover="this.style.borderColor='#dc2626';this.style.color='#dc2626';"
                    onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--text)';">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </header>

    <main class="main-content">
        @if (session('status'))
            <div class="flash-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="flash-error">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
