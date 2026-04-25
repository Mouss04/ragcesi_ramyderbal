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

        /* ── Notification bell ── */
        @keyframes bell-ring {
            0%   { transform: rotate(0deg); }
            10%  { transform: rotate(18deg); }
            20%  { transform: rotate(-16deg); }
            30%  { transform: rotate(14deg); }
            40%  { transform: rotate(-10deg); }
            50%  { transform: rotate(6deg); }
            60%  { transform: rotate(-3deg); }
            70%  { transform: rotate(2deg); }
            80%  { transform: rotate(-1deg); }
            100% { transform: rotate(0deg); }
        }
        @keyframes badge-pop {
            0%   { transform: scale(0); opacity: 0; }
            60%  { transform: scale(1.35); }
            80%  { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes badge-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.55); }
            50%       { box-shadow: 0 0 0 6px rgba(220,38,38,0); }
        }
        @keyframes dropdown-in {
            from { opacity: 0; transform: translateY(-8px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)   scale(1); }
        }
        @keyframes item-slide-in {
            from { opacity: 0; transform: translateX(12px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes toast-in {
            from { opacity: 0; transform: translateY(20px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }
        @keyframes rag-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes rag-pulse-ring {
            0%   { transform: scale(.7);  opacity: .8; }
            70%  { transform: scale(1.5); opacity: 0;  }
            100% { transform: scale(.7);  opacity: 0;  }
        }
        @keyframes toast-out {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(12px); }
        }

        .notif-wrap { position: relative; }

        .notif-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            border: 1.5px solid var(--line);
            background: #fff;
            cursor: pointer;
            transition: border-color 160ms, background 160ms, box-shadow 160ms;
            position: relative;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .notif-btn:hover {
            border-color: var(--teal);
            background: var(--teal-soft);
            box-shadow: 0 2px 12px var(--teal-shadow);
        }
        .notif-btn.has-notif svg {
            animation: bell-ring 1s cubic-bezier(0.36,0.07,0.19,0.97) both;
        }
        .notif-btn svg { color: var(--teal-dark); }

        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid #fff;
            animation: badge-pop 0.4s cubic-bezier(0.34,1.56,0.64,1) both,
                       badge-pulse 2.2s ease-in-out 0.5s infinite;
        }

        .notif-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 360px;
            background: #fff;
            border: 1.5px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.14), 0 4px 16px rgba(0,0,0,0.07);
            z-index: 1000;
            overflow: hidden;
        }
        .notif-dropdown.open {
            display: block;
            animation: dropdown-in 0.22s cubic-bezier(0.34,1.2,0.64,1) both;
        }

        .notif-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.1rem 0.75rem;
            background: linear-gradient(135deg, var(--teal-soft) 0%, #fff 100%);
            border-bottom: 1px solid var(--line);
        }
        .notif-header-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .notif-header-icon {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: var(--teal);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }
        .notif-header-title {
            font-size: 0.92rem;
            font-weight: 800;
            color: var(--text);
        }
        .notif-header-sub {
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 500;
        }
        .notif-mark-read {
            font-size: 0.75rem;
            color: var(--teal);
            cursor: pointer;
            background: rgba(var(--teal), 0.07);
            border: 1px solid var(--line);
            border-radius: 7px;
            font: inherit;
            font-weight: 700;
            padding: 0.3rem 0.65rem;
            transition: background 140ms, border-color 140ms;
        }
        .notif-mark-read:hover {
            background: var(--teal-soft);
            border-color: var(--teal);
        }

        .notif-list {
            max-height: 360px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--line) transparent;
        }
        .notif-list::-webkit-scrollbar { width: 4px; }
        .notif-list::-webkit-scrollbar-track { background: transparent; }
        .notif-list::-webkit-scrollbar-thumb { background: var(--line); border-radius: 99px; }

        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.85rem 1.1rem;
            border-bottom: 1px solid #f0f4f4;
            text-decoration: none;
            color: inherit;
            transition: background 150ms;
            animation: item-slide-in 0.25s ease both;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item.unread { background: linear-gradient(90deg, rgba(12,112,112,0.05) 0%, transparent 100%); }
        .notif-item:hover { background: var(--teal-soft); }

        .notif-item-dot {
            flex-shrink: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--teal);
            margin-top: 5px;
            box-shadow: 0 0 0 3px rgba(12,112,112,0.15);
        }
        .notif-item-content { flex: 1; min-width: 0; }
        .notif-item-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.18rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .notif-item-body {
            font-size: 0.77rem;
            color: var(--muted);
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .notif-item-time {
            font-size: 0.7rem;
            color: #b0c4c4;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .notif-footer {
            padding: 0.6rem 1.1rem;
            border-top: 1px solid var(--line);
            background: #fafcfc;
        }
        .notif-footer a {
            display: block;
            text-align: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--teal);
            text-decoration: none;
            padding: 0.35rem;
            border-radius: 8px;
            transition: background 140ms;
        }
        .notif-footer a:hover { background: var(--teal-soft); }

        .notif-empty {
            padding: 2.5rem 1rem;
            text-align: center;
            color: var(--muted);
        }
        .notif-empty-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--teal-soft);
            display: grid;
            place-items: center;
            margin: 0 auto 0.75rem;
        }
        .notif-empty p { font-size: 0.85rem; font-weight: 600; margin: 0; }
        .notif-empty span { font-size: 0.75rem; }

        /* ── Toast popup ── */
        .notif-toast-wrap {
            position: fixed;
            bottom: 1.4rem;
            right: 1.4rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            pointer-events: none;
        }
        .notif-toast {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            background: #fff;
            border: 1.5px solid var(--line);
            border-left: 4px solid var(--teal);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.13);
            min-width: 280px;
            max-width: 340px;
            pointer-events: all;
            animation: toast-in 0.35s cubic-bezier(0.34,1.2,0.64,1) both;
        }
        .notif-toast.hiding {
            animation: toast-out 0.3s ease forwards;
        }
        .notif-toast-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--teal-soft);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }
        .notif-toast-body { flex: 1; min-width: 0; }
        .notif-toast-title { font-size: 0.82rem; font-weight: 700; color: var(--text); margin-bottom: 0.18rem; }
        .notif-toast-text  { font-size: 0.76rem; color: var(--muted); line-height: 1.4; }
        .notif-toast-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted);
            padding: 2px;
            line-height: 1;
            flex-shrink: 0;
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
           class="nav-item {{ request()->routeIs('admin.documents.index') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            {{ __('Documents') }}
        </a>

        <a href="{{ route('admin.documents.consult') }}"
           class="nav-item {{ request()->routeIs('admin.documents.consult') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            {{ __('Consult documents') }}
        </a>

        <a href="{{ route('admin.comments.index') }}"
           class="nav-item {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            {{ __('Comments') }}
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

        {{-- ── RAG indexing indicator (shown via JS when approving a comment) ── --}}
        <div id="rag-index-indicator" style="display:none;align-items:center;gap:.8rem;background:#fff;border:1.5px solid #b2dbd8;border-radius:14px;padding:.5rem 1rem .5rem .7rem;box-shadow:0 2px 14px rgba(12,112,112,.12);">
            {{-- Animated pulse dot --}}
            <div style="position:relative;width:28px;height:28px;flex-shrink:0;">
                <span style="position:absolute;inset:0;border-radius:50%;background:rgba(12,112,112,.12);animation:rag-pulse-ring 1.4s ease-out infinite;"></span>
                <span style="position:absolute;inset:5px;border-radius:50%;background:linear-gradient(135deg,#0c7070,#3db8a4);display:flex;align-items:center;justify-content:center;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56" style="animation:rag-spin 1s linear infinite;transform-origin:center;"/>
                    </svg>
                </span>
            </div>
            {{-- Text + bar --}}
            <div>
                <div style="font-size:.78rem;font-weight:700;color:#0c7070;letter-spacing:.01em;white-space:nowrap;">{{ __('Indexing in the RAG pipeline…') }}</div>
                <div style="display:flex;align-items:center;gap:.45rem;margin-top:5px;">
                    <div style="height:4px;width:120px;background:#dceeed;border-radius:99px;overflow:hidden;">
                        <div id="rag-index-bar" style="height:100%;width:0;background:linear-gradient(90deg,#0c7070 0%,#3db8a4 60%,#a8e0da 100%);border-radius:99px;transition:width .35s ease;"></div>
                    </div>
                    <span id="rag-index-pct" style="font-size:.68rem;font-weight:700;color:#5da8a4;min-width:28px;">0%</span>
                </div>
            </div>
        </div>

        <div class="user-chip">

            {{-- ── Notification bell ── --}}
            @php
                $allUnread       = auth()->user()->unreadNotifications;
                $pendingIds      = \App\Models\Comment::where('status', 'PENDING')->pluck('id')->flip()->all();
                $unreadNotifications = $allUnread->filter(fn($n) => isset($pendingIds[$n->data['comment_id'] ?? null]));
                $unreadCount         = $unreadNotifications->count();
                // Auto-dismiss notifications for comments that are no longer pending
                $staleIds = $allUnread->filter(fn($n) => !isset($pendingIds[$n->data['comment_id'] ?? null]))->pluck('id')->all();
                if (!empty($staleIds)) {
                    \DB::table('notifications')->whereIn('id', $staleIds)->update(['read_at' => now()]);
                }
                $unreadNotifications = $unreadNotifications->take(15);
            @endphp
            <div class="notif-wrap">
                <button class="notif-btn {{ $unreadCount > 0 ? 'has-notif' : '' }}" id="notifBtn" aria-label="{{ __('Notifications') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span class="notif-badge" id="notifBadge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </button>

                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <div class="notif-header-left">
                            <div class="notif-header-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                            </div>
                            <div>
                                <div class="notif-header-title">{{ __('Notifications') }}</div>
                                <div class="notif-header-sub">{{ $unreadCount > 0 ? $unreadCount . ' ' . __('unread') : __('All caught up') }}</div>
                            </div>
                        </div>
                        @if($unreadCount > 0)
                            <button class="notif-mark-read" id="notifMarkAll">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:2px;">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                {{ __('Mark all read') }}
                            </button>
                        @endif
                    </div>

                    <div class="notif-list" id="notifList">
                        @forelse($unreadNotifications as $i => $notif)
                            <a href="{{ route('admin.comments.index') }}"
                               class="notif-item unread"
                               style="animation-delay: {{ $i * 40 }}ms"
                               data-id="{{ $notif->id }}">
                                <span class="notif-item-dot"></span>
                                <div class="notif-item-content">
                                    <div class="notif-item-title">
                                        💬 {{ $notif->data['document_title'] ?? '—' }}
                                    </div>
                                    <div class="notif-item-body">
                                        <strong>{{ $notif->data['author_name'] ?? '' }}</strong>:
                                        {{ $notif->data['excerpt'] ?? '' }}
                                    </div>
                                    <div class="notif-item-time">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        {{ $notif->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="notif-empty">
                                <div class="notif-empty-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:var(--teal);">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                    </svg>
                                </div>
                                <p>{{ __('All caught up!') }}</p>
                                <span>{{ __('No new notifications') }}</span>
                            </div>
                        @endforelse
                    </div>

                    <div class="notif-footer">
                        <a href="{{ route('admin.comments.index') }}">{{ __('View all comments') }} →</a>
                    </div>
                </div>
            </div>

            {{-- Toast container --}}
            <div class="notif-toast-wrap" id="notifToastWrap"></div>

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
<script>
(function () {
    const btn        = document.getElementById('notifBtn');
    const dropdown   = document.getElementById('notifDropdown');
    const markAll    = document.getElementById('notifMarkAll');
    const toastWrap  = document.getElementById('notifToastWrap');
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
    const MARK_URL   = '{{ route('notifications.markAllRead') }}';
    const POLL_URL   = '{{ route('notifications.unreadCount') }}';
    let   lastCount  = {{ $unreadCount }};
    /* SCRIPT_BLOCK_START */

    /* ── Bell toggle ── */
    if (btn && dropdown) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const opening = !dropdown.classList.contains('open');
            dropdown.classList.toggle('open');
            if (opening) btn.classList.remove('has-notif');
        });
        document.addEventListener('click', function () {
            dropdown.classList.remove('open');
        });
        dropdown.addEventListener('click', function (e) { e.stopPropagation(); });
    }

    /* ── Mark all read ── */
    if (markAll) {
        markAll.addEventListener('click', function () {
            fetch(MARK_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            }).then(function () { window.location.reload(); });
        });
    }

    /* ── Toast helper ── */
    function showToast(title, body) {
        if (!toastWrap) return;
        const toast = document.createElement('div');
        toast.className = 'notif-toast';
        toast.innerHTML =
            '<div class="notif-toast-icon">' +
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                    '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>' +
                    '<path d="M13.73 21a2 2 0 0 1-3.46 0"/>' +
                '</svg>' +
            '</div>' +
            '<div class="notif-toast-body">' +
                '<div class="notif-toast-title">' + title + '</div>' +
                '<div class="notif-toast-text">'  + body  + '</div>' +
            '</div>' +
            '<button class="notif-toast-close" aria-label="Dismiss">' +
                '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
            '</button>';
        toast.querySelector('.notif-toast-close').addEventListener('click', function () { dismissToast(toast); });
        toastWrap.appendChild(toast);
        setTimeout(function () { dismissToast(toast); }, 6000);
    }

    function dismissToast(toast) {
        toast.classList.add('hiding');
        toast.addEventListener('animationend', function () { toast.remove(); }, { once: true });
    }

    /* ── Ring bell animation ── */
    function ringBell() {
        if (!btn) return;
        btn.classList.remove('has-notif');
        void btn.offsetWidth;
        btn.classList.add('has-notif');
    }

    /* ── Notification sound (Web Audio API — no file needed) ── */
    var audioCtx = null;

    /* Browsers suspend AudioContext until first user gesture.
       We resume it on any click/key so it's ready when a notification arrives. */
    function unlockAudio() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
    }
    document.addEventListener('click',   unlockAudio, { once: false, passive: true });
    document.addEventListener('keydown',  unlockAudio, { once: false, passive: true });

    function playNotifSound() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var ctx = audioCtx;

            function doPlay() {
                function note(freq, startTime, duration, vol) {
                    var osc  = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, startTime);
                    gain.gain.setValueAtTime(0, startTime);
                    gain.gain.linearRampToValueAtTime(vol, startTime + 0.01);
                    gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                    osc.start(startTime);
                    osc.stop(startTime + duration);
                }
                var t = ctx.currentTime;
                note(880,  t,        0.14, 0.5);   /* A5  — first ding  */
                note(1108, t + 0.15, 0.20, 0.40);  /* C#6 — second ding */
            }

            /* If still suspended (no user gesture yet), resume first then play */
            if (ctx.state === 'suspended') {
                ctx.resume().then(doPlay).catch(function () {});
            } else {
                doPlay();
            }
        } catch (_) {}
    }

    /* ── Handle a notification payload ── */
    function handleUpdate(data) {
        const newCount = data.count || 0;
        if (newCount > lastCount) {
            ringBell();
            playNotifSound();
            let badge = document.getElementById('notifBadge');
            if (!badge && btn) {
                badge = document.createElement('span');
                badge.id        = 'notifBadge';
                badge.className = 'notif-badge';
                btn.appendChild(badge);
            }
            if (badge) badge.textContent = newCount > 99 ? '99+' : newCount;
            if (data.latest) {
                showToast(
                    '{{ __('New comment pending') }}',
                    (data.latest.author_name || '') + ': ' + (data.latest.excerpt || '')
                );
            } else {
                const diff = newCount - lastCount;
                showToast('{{ __('New notification') }}', diff + ' {{ __('new comment(s) awaiting moderation') }}');
            }
        }
        lastCount = newCount;
    }

    /* ── Lightweight polling every 8 s ── */
    /* Each fetch completes in < 100 ms — safe for php artisan serve */
    function poll() {
        fetch(POLL_URL, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) { if (data) handleUpdate(data); })
            .catch(function () {});
    }

    setInterval(poll, 8000);
})();
</script>
</body>
</html>
