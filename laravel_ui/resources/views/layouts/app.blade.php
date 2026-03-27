<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RAG Company Portal' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Archivo+Black&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f7f7f3;
            --panel: #ffffff;
            --text: #1f2a2e;
            --muted: #61747c;
            --line: #dfe6e8;
            --primary: #0f766e;
            --primary-dark: #0a5a54;
            --danger: #b42318;
            --soft: #ecf7f6;
            --accent: #f5b700;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 8% 10%, #e6f4f2 0%, transparent 30%),
                radial-gradient(circle at 92% 84%, #fff3cf 0%, transparent 28%),
                var(--bg);
        }

        .shell {
            width: min(1200px, 94vw);
            margin: 1.2rem auto 2.5rem;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 0.9rem 1rem;
            backdrop-filter: blur(8px);
            box-shadow: 0 10px 25px rgba(18, 30, 33, 0.06);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            text-decoration: none;
            color: var(--text);
            font-weight: 800;
        }

        .brand-badge {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), #14b8a6);
            font-family: 'Archivo Black', sans-serif;
            letter-spacing: 0.04em;
            animation: riseIn 500ms ease both;
        }

        .nav {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .nav a, .nav button {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
            border-radius: 10px;
            padding: 0.5rem 0.75rem;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: 160ms ease;
        }

        .nav a:hover, .nav button:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
        }

        .content {
            margin-top: 1rem;
            display: grid;
            gap: 1rem;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 1.1rem;
            box-shadow: 0 12px 28px rgba(20, 32, 35, 0.06);
            animation: riseIn 520ms ease both;
        }

        .title {
            margin: 0;
            font-size: clamp(1.2rem, 2.4vw, 1.8rem);
        }

        .subtitle {
            margin: 0.35rem 0 0;
            color: var(--muted);
        }

        .status {
            border: 1px solid #b8e7e2;
            background: #eefcf9;
            color: #0f766e;
            padding: 0.65rem 0.8rem;
            border-radius: 12px;
            font-weight: 600;
        }

        .error {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
            padding: 0.65rem 0.8rem;
            border-radius: 12px;
            font-weight: 600;
        }

        .grid {
            display: grid;
            gap: 1rem;
        }

        .grid.cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid.cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 0.9rem;
            background: #fff;
        }

        label { display: block; margin-bottom: 0.35rem; font-weight: 600; }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 0.6rem 0.7rem;
            font: inherit;
            color: var(--text);
            background: #fff;
        }

        textarea { min-height: 120px; resize: vertical; }

        .btn {
            border: 1px solid transparent;
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            transition: 160ms ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }

        .btn-muted {
            border-color: var(--line);
            color: var(--text);
            background: #fff;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.8rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            text-align: left;
            padding: 0.7rem;
            border-bottom: 1px solid var(--line);
            vertical-align: middle;
        }

        th { background: #f3f7f8; }

        .pill {
            display: inline-block;
            border-radius: 999px;
            padding: 0.2rem 0.6rem;
            background: var(--soft);
            color: var(--primary-dark);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .metric {
            font-size: 1.7rem;
            margin: 0.25rem 0 0;
            font-weight: 800;
        }

        @keyframes riseIn {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 900px) {
            .grid.cols-2, .grid.cols-3 { grid-template-columns: 1fr; }
            .topbar { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
            <span class="brand-badge">RG</span>
            <span>RAG Company Portal</span>
        </a>

        <nav class="nav">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    <a href="{{ route('admin.users.index') }}">Users</a>
                    <a href="{{ route('admin.documents.index') }}">Documents</a>
                @endif
                <a href="{{ route('employee.dashboard') }}">Ask AI</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </nav>
    </header>

    <main class="content">
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
