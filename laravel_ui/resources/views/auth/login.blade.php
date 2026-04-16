<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — Witrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom, #ffffff 0%, #c8dedd 40%, #4d8585 100%);
        }

        /* ── Card ── */
        .login-card {
            display: flex;
            width: min(820px, 95vw);
            min-height: 480px;
            border-radius: 28px;
            overflow: hidden;
            background: #0e2622;
            box-shadow: 0 24px 80px rgba(0,0,0,.45), 0 4px 16px rgba(0,0,0,.2);
            animation: riseIn 500ms cubic-bezier(.22,.68,0,1.2) both;
        }

        /* ── Left: form ── */
        .login-left {
            flex: 1;
            padding: 3.2rem 2.8rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #0e2622;
        }

        /* Brand row */
        .brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 2.2rem;
        }
        .brand-logo {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }
        .brand-name {
            font-size: 1.15rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.02em;
        }
        .brand-name span { color: #38c87; }

        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            text-decoration: none;
            font-size: .8rem;
            font-weight: 600;
            color: rgba(168,216,213,.55);
            transition: color 150ms;
            padding: .3rem 0;
        }
        .btn-home:hover { color: #a8d8d5; }
        .btn-home svg { transition: transform 150ms; }
        .btn-home:hover svg { transform: translateX(-3px); }

        .welcome-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.025em;
            margin-bottom: .35rem;
        }
        .welcome-sub {
            font-size: .84rem;
            color: rgba(168,216,213,.7);
            margin-bottom: 1.8rem;
        }

        /* alerts */
        .alert-error {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            color: #fca5a5;
            border-radius: 12px;
            padding: .6rem .85rem;
            font-size: .83rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .alert-status {
            background: rgba(61,140,135,.15);
            border: 1px solid rgba(61,140,135,.35);
            color: #a8d8d5;
            border-radius: 12px;
            padding: .6rem .85rem;
            font-size: .83rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* fields */
        .field { margin-bottom: .95rem; }
        .field-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: rgba(168,216,213,.8);
            margin-bottom: .4rem;
            letter-spacing: .01em;
        }
        .field-wrap { position: relative; }
        .field-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            transition: color 180ms;
        }
        .field-wrap:focus-within .field-icon { color: #3d8c87; }

        .field input {
            width: 100%;
            border: 1.5px solid #1a4542;
            border-radius: 12px;
            padding: .75rem 1rem .75rem 2.7rem;
            font: inherit;
            font-size: .9rem;
            color: #e5e7eb;
            background: #091e1c;
            outline: none;
            transition: border-color 180ms, box-shadow 180ms, background 180ms;
        }
        .field input::placeholder { color: rgba(168,216,213,.3); }
        .field input:focus {
            border-color: #3d8c87;
            background: #091e1c;
            box-shadow: 0 0 0 3px rgba(61,140,135,.22);
        }

        .forgot-wrap {
            text-align: right;
            margin: -.2rem 0 1.4rem;
        }
        .forgot-link {
            font-size: .81rem;
            color: rgba(168,216,213,.55);
            text-decoration: none;
            transition: color 150ms;
        }
        .forgot-link:hover { color: #a8d8d5; }

        /* Button */
        .btn-login {
            width: 100%;
            padding: .85rem 1.5rem;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #2f7470, #3d8c87);
            color: #fff;
            font: inherit;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            letter-spacing: .02em;
            box-shadow: 0 4px 18px rgba(61,140,135,.35);
            transition: transform 150ms, box-shadow 150ms;
        }
        .btn-login:hover  { background: linear-gradient(135deg, #25605c, #2f7470); transform: translateY(-2px); box-shadow: 0 8px 26px rgba(61,140,135,.45); }
        .btn-login:active { transform: translateY(0);    box-shadow: 0 2px 10px rgba(61,140,135,.25); }

        .signup-text {
            text-align: center;
            margin-top: 1.2rem;
            font-size: .82rem;
            color: rgba(168,216,213,.45);
        }
        .signup-text a {
            color: #a8d8d5;
            font-weight: 600;
            text-decoration: none;
        }
        .signup-text a:hover { text-decoration: underline; }

        /* ── Right: brand panel ── */
        .login-right {
            flex: 1;
            margin: 14px;
            border-radius: 20px;
            background: linear-gradient(160deg, #ffffff 0%, #d4eeec 45%, #3d8c87 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Watermark logos */
        .wm-logo {
            position: absolute;
            object-fit: contain;
            opacity: .09;
            pointer-events: none;
            user-select: none;
        }
        .login-right::after {
            content: '';
            position: absolute;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(61,140,135,.08) 0%, transparent 70%);
            top: -60px; right: -60px;
        }

        .panel-logo {
            width: 110px;
            height: 110px;
            object-fit: contain;
            margin-bottom: 1.8rem;
            filter: drop-shadow(0 6px 18px rgba(47,116,112,.2));
            animation: floatLogo 5s ease-in-out infinite;
        }

        .panel-title {
            font-size: 2rem;
            font-weight: 800;
            color: #0e2622;
            letter-spacing: -.03em;
            margin-bottom: .5rem;
            text-align: center;
        }
        .panel-title span { color: #0e2622; }

        .panel-desc {
            font-size: 1rem;
            color: #4b7270;
            text-align: center;
            line-height: 1.65;
            max-width: 26ch;
            margin-bottom: 1.4rem;
        }

        .panel-quote {
            font-size: .95rem;
            color: #0e2622;
            text-align: center;
            font-weight: 800;
            font-style: normal;
            line-height: 1.6;
            max-width: 26ch;
            margin-top: 1rem;
        }

        /* Feature chips */
        .chips {
            display: flex;
            flex-direction: column;
            gap: .55rem;
            width: 100%;
            max-width: 200px;
        }
        .chip {
            display: flex;
            align-items: center;
            gap: .6rem;
            background: rgba(255,255,255,.7);
            border: 1px solid rgba(61,140,135,.25);
            border-radius: 50px;
            padding: .48rem .9rem;
            font-size: .78rem;
            font-weight: 600;
            color: #1a4542;
            backdrop-filter: blur(8px);
        }
        .chip svg { flex-shrink: 0; color: #3d8c87; }

        /* ── Keyframes ── */
        @keyframes riseIn {
            from { opacity: 0; transform: translateY(26px) scale(.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1);   }
        }
        @keyframes floatLogo {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-10px); }
        }

        @media (max-width: 640px) {
            .login-card  { flex-direction: column; }
            .login-right { margin: 0 14px 14px; min-height: 220px; border-radius: 0 0 18px 18px; }
            .panel-logo  { width: 85px; height: 85px; margin-bottom: 1rem; }
            .chips       { flex-direction: row; flex-wrap: wrap; justify-content: center; max-width: none; }
            .login-left  { padding: 2rem 1.8rem; }
        }
    </style>
</head>
<body>

<div class="login-card">

    {{-- Left: form panel --}}
    <div class="login-left">

    <a href="{{ url('/') }}" class="btn-home">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        Retour à l'accueil
    </a>

    <div class="brand" style="margin-top:1.4rem;">
        <span class="brand-name">Witrack <span style="color:#3d8c87;">Relie les points</span></span>
    </div>

    <h1 class="welcome-title">Connexion à votre compte</h1>
    <p class="welcome-sub">Connectez-vous pour accéder à votre portail de connaissances.</p>

    @if (session('status'))
        <div class="alert-status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div class="field">
            <label class="field-label" for="name">Nom d'utilisateur</label>
            <div class="field-wrap">
                <svg class="field-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <input id="name" name="name" value="{{ old('name') }}" autocomplete="username" required autofocus placeholder="votre.nom">
            </div>
        </div>

        <div class="field">
            <label class="field-label" for="password">Mot de passe</label>
            <div class="field-wrap">
                <svg class="field-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn-login">
            Se connecter
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <polygon points="5 3 19 12 5 21 5 3"/>
            </svg>
        </button>
    </form>

</div>



    {{-- Right: brand / feature panel --}}
   <div class="login-right">
    <!-- Logos en arrière-plan (filigrane) -->
    <img src="{{ asset('bagroung_logo.png') }}" class="wm-logo" style="width:160px;bottom:-25px;left:-30px;transform:rotate(-15deg)" alt="">
    <img src="{{ asset('bagroung_logo.png') }}" class="wm-logo" style="width:110px;top:10px;right:-20px;transform:rotate(20deg)" alt="">
    <img src="{{ asset('bagroung_logo.png') }}" class="wm-logo" style="width:80px;top:45%;left:5px;transform:rotate(-5deg)" alt="">
    <img src="{{ asset('bagroung_logo.png') }}" class="wm-logo" style="width:70px;bottom:15%;right:8px;transform:rotate(10deg)" alt="">
    <img src="{{ asset('bagroung_logo.png') }}" alt="Logo Witrack" class="panel-logo">

    <p class="panel-title">Witrack</p>
    <p class="panel-desc">Votre portail de connaissances d’entreprise, alimenté par l’IA.</p>

    <p class="panel-quote">
        Transformez vos documents en réponses intelligentes.
    </p>
</div>

</div>

</body>
</html>
