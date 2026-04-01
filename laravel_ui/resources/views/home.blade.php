<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Witrack — Links the Dots</title>
    <meta name="description" content="Witrack — posez vos questions, vos documents répondent.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --teal:   #3d8c87;
            --teal-d: #2a6e6a;
            --dark:   #0e2622;
            --teal-l: #a8d8d5;
            --teal-s: #e8f5f4;
            --bg:     #fafcfc;
            --radius: 20px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: #111;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── NAVBAR ── */
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .85rem 4rem;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(61,140,135,.10);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 24px rgba(14,38,34,.06);
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: .55rem;
            text-decoration: none;
        }
        .nav-brand-logo { width: 38px; height: 38px; object-fit: contain; }
        .nav-brand-text { display: flex; flex-direction: column; line-height: 1.1; }
        .nav-brand-name { font-size: 1.1rem; font-weight: 800; color: #0e2622; letter-spacing: -.01em; }
        .nav-brand-sub  { font-size: .58rem; font-weight: 700; letter-spacing: .12em; color: var(--teal); text-transform: uppercase; }

        .nav-links { display: flex; align-items: center; gap: 2rem; list-style: none; }
        .nav-links a {
            font-size: .9rem; font-weight: 500; color: #374151;
            text-decoration: none; transition: color 150ms;
            position: relative;
        }
        .nav-links a::after {
            content:''; position:absolute; left:0; bottom:-4px;
            width:0; height:2px; background:var(--teal);
            border-radius:2px; transition: width 250ms ease;
        }
        .nav-links a:hover::after,
        .nav-links a.active::after { width:100%; }
        .nav-links a:hover, .nav-links a.active { color: var(--teal); }

        .nav-right { display: flex; align-items: center; gap: .75rem; }
        .nav-login-link {
            text-decoration: none; display: inline-flex;
            align-items: center; gap: .45rem;
            padding: .5rem 1.25rem;
            border-radius: 50px;
            background: var(--dark);
            color: #fff;
            font-size: .88rem; font-weight: 700;
            letter-spacing: .01em;
            border: 2px solid var(--dark);
            transition: background 200ms, color 200ms, transform 150ms, box-shadow 200ms;
            box-shadow: 0 2px 12px rgba(14,38,34,.2);
        }
        .nav-login-link:hover {
            background: transparent;
            color: var(--dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 18px rgba(14,38,34,.1);
        }

        /* ── HERO ── */
        .hero {
            position: relative;
            min-height: 88vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(160deg, #ffffff 0%, #e0f2f1 30%, #b2dfdb 60%, #4d9994 100%);
        }

        /* animated mesh blobs */
        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .45;
            pointer-events: none;
            will-change: transform;
        }
        .hero-blob-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, #3d8c87 0%, transparent 70%);
            top: -120px; right: -100px;
            animation: blobDrift1 14s ease-in-out infinite;
        }
        .hero-blob-2 {
            width: 450px; height: 450px;
            background: radial-gradient(circle, #a8d8d5 0%, transparent 70%);
            bottom: -80px; left: -60px;
            animation: blobDrift2 18s ease-in-out infinite;
        }
        .hero-blob-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #0e2622 0%, transparent 70%);
            top: 40%; left: 50%;
            opacity: .12;
            animation: blobDrift3 10s ease-in-out infinite;
        }
        @keyframes blobDrift1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-40px,30px) scale(1.08)} }
        @keyframes blobDrift2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(50px,-40px) scale(1.1)} }
        @keyframes blobDrift3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-30px,-20px) scale(1.15)} }

        /* floating dots */
        .hero-dots {
            position: absolute; inset: 0;
            pointer-events: none; overflow: hidden;
        }
        .hero-dot {
            position: absolute;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(61,140,135,.35);
            animation: dotFloat linear infinite;
        }
        .hero-dot:nth-child(1)  { left:10%; top:20%; animation-duration:8s; animation-delay:0s; width:5px;height:5px; }
        .hero-dot:nth-child(2)  { left:25%; top:70%; animation-duration:12s; animation-delay:1s; width:4px;height:4px; background:rgba(14,38,34,.2); }
        .hero-dot:nth-child(3)  { left:60%; top:15%; animation-duration:10s; animation-delay:2s; width:7px;height:7px; }
        .hero-dot:nth-child(4)  { left:80%; top:60%; animation-duration:9s;  animation-delay:.5s; width:5px;height:5px; background:rgba(168,216,213,.6); }
        .hero-dot:nth-child(5)  { left:45%; top:80%; animation-duration:11s; animation-delay:3s; width:4px;height:4px; }
        .hero-dot:nth-child(6)  { left:90%; top:30%; animation-duration:7s;  animation-delay:1.5s; width:6px;height:6px; background:rgba(14,38,34,.15); }
        .hero-dot:nth-child(7)  { left:15%; top:50%; animation-duration:13s; animation-delay:4s; width:3px;height:3px; }
        .hero-dot:nth-child(8)  { left:70%; top:40%; animation-duration:9s;  animation-delay:2.5s; width:5px;height:5px; background:rgba(168,216,213,.45); }
        @keyframes dotFloat {
            0%   { transform: translateY(0) scale(1); opacity:.6; }
            50%  { transform: translateY(-30px) scale(1.3); opacity:1; }
            100% { transform: translateY(0) scale(1); opacity:.6; }
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4rem;
            padding: 2rem 5vw;
            max-width: 1100px;
            width: 100%;
        }

        .hero-illustration {
            flex: 1;
            max-width: 360px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-illustration img {
            width: 100%;
            max-width: 300px;
            object-fit: contain;
            filter: drop-shadow(0 20px 50px rgba(61,140,135,.35));
            animation: floatNet 6s ease-in-out infinite;
        }
        @keyframes floatNet {
            0%,100% { transform: translateY(0) rotate(0deg); }
            50%     { transform: translateY(-18px) rotate(2deg); }
        }

        /* glowing ring behind logo */
        .hero-glow {
            position: relative;
        }
        .hero-glow::before {
            content: '';
            position: absolute;
            inset: -30px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(61,140,135,.25) 0%, transparent 65%);
            animation: pulseGlow 4s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%,100% { transform: scale(1); opacity:.6; }
            50%     { transform: scale(1.15); opacity:1; }
        }

        /* ── HERO CHAT MOCKUP ── */
        .hero-mockup {
            flex: 1;
            max-width: 400px;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 28px 80px rgba(14,38,34,.2), 0 4px 20px rgba(61,140,135,.18);
            overflow: hidden;
            border: 1.5px solid rgba(61,140,135,.12);
            animation: floatNet 6s ease-in-out infinite;
        }
        .mockup-topbar {
            background: var(--dark);
            padding: .85rem 1.2rem;
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .mockup-logo {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: rgba(255,255,255,.12);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .mockup-logo img { width:20px; height:20px; object-fit:contain; filter: brightness(0) invert(1); }
        .mockup-title { font-size: .82rem; font-weight: 700; color: #fff; }
        .mockup-status { font-size: .68rem; color: rgba(168,216,213,.65); display:flex; align-items:center; gap:4px; }
        .mockup-online { width:6px; height:6px; border-radius:50%; background:#4ade80; }
        .mockup-winbtns { margin-left:auto; display:flex; gap:5px; }
        .mockup-winbtns span { width:10px;height:10px; border-radius:50%; background:rgba(255,255,255,.2); }
        .mockup-winbtns span:first-child { background:#ef4444; }
        .mockup-winbtns span:nth-child(2) { background:#f59e0b; }
        .mockup-winbtns span:last-child { background:#4ade80; }
        .mockup-body {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            min-height: 200px;
        }
        .mockup-msg { display: flex; gap: .5rem; align-items: flex-end; }
        .mockup-msg.right { flex-direction: row-reverse; }
        .mockup-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            flex-shrink: 0; display:flex; align-items:center; justify-content:center;
            font-size: .6rem; font-weight: 800;
        }
        .mockup-avatar.bot { background: var(--teal-s); color: var(--teal); }
        .mockup-avatar.usr { background: var(--dark); color: #fff; }
        .mockup-bubble {
            padding: .6rem .85rem;
            border-radius: 14px;
            font-size: .78rem;
            line-height: 1.5;
            max-width: 82%;
        }
        .mockup-bubble.bot {
            background: var(--teal-s);
            color: var(--dark);
            border-bottom-left-radius: 4px;
        }
        .mockup-bubble.usr {
            background: linear-gradient(135deg, var(--teal), var(--teal-d));
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .mockup-source { margin-top: .35rem; display: flex; gap: .3rem; flex-wrap: wrap; }
        .mockup-chip {
            background: rgba(61,140,135,.1);
            color: var(--teal);
            border: 1px solid rgba(61,140,135,.18);
            border-radius: 6px;
            padding: .1rem .45rem;
            font-size: .65rem;
            font-weight: 700;
        }
        .mockup-typing {
            display: flex;
            gap: .5rem;
            align-items: flex-end;
            opacity: 0;
            animation: typeIn 1s ease 2.8s forwards;
        }
        @keyframes typeIn { to { opacity: 1; } }
        .mockup-typing-dots {
            background: var(--teal-s);
            border-radius: 14px;
            padding: .6rem .85rem;
            display: flex; gap: 4px; align-items: center;
        }
        .mockup-typing-dots span {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--teal);
            animation: typingBounce 1.2s ease-in-out infinite;
        }
        .mockup-typing-dots span:nth-child(2) { animation-delay: .2s; }
        .mockup-typing-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes typingBounce {
            0%,60%,100% { transform: translateY(0); }
            30%          { transform: translateY(-5px); }
        }
        .mockup-input {
            background: #f8fafa;
            border-top: 1px solid var(--teal-s);
            padding: .65rem 1rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .mockup-input-field {
            flex: 1;
            background: #fff;
            border: 1.5px solid #e8f0ef;
            border-radius: 10px;
            padding: .45rem .75rem;
            font-size: .75rem;
            color: #9ca3af;
        }
        .mockup-send {
            width: 30px; height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--teal), var(--teal-d));
            display: flex; align-items:center; justify-content:center;
            flex-shrink: 0;
        }

        .hero-content { flex: 1; max-width: 500px; }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem .85rem;
            background: rgba(255,255,255,.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(61,140,135,.2);
            border-radius: 50px;
            font-size: .72rem;
            font-weight: 700;
            color: var(--teal);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 1.1rem;
            animation: fadeSlideUp .6s ease both;
        }
        .hero-badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--teal);
            animation: pulseDot 2s ease-in-out infinite;
        }
        @keyframes pulseDot { 0%,100%{opacity:.5;transform:scale(1)} 50%{opacity:1;transform:scale(1.3)} }

        .hero-avec {
            font-size: 3.2rem;
            font-weight: 900;
            color: var(--dark);
            letter-spacing: -.03em;
            line-height: 1.08;
            margin-bottom: .6rem;
            animation: fadeSlideUp .6s ease .1s both;
        }
        .hero-avec span {
            background: linear-gradient(135deg, var(--teal), #2ac4b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-tagline {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2d4a48;
            line-height: 1.5;
            margin-bottom: 2rem;
            animation: fadeSlideUp .6s ease .2s both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: fadeSlideUp .6s ease .3s both;
        }

        @keyframes fadeSlideUp {
            from { opacity:0; transform:translateY(18px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .9rem 2rem;
            background: linear-gradient(135deg, var(--dark), #1a4542);
            color: #fff;
            font: inherit; font-size: .95rem; font-weight: 700;
            border: none; border-radius: 14px; cursor: pointer;
            text-decoration: none; letter-spacing: .01em;
            transition: transform 200ms, box-shadow 200ms;
            box-shadow: 0 4px 20px rgba(14,38,34,.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(14,38,34,.4);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .9rem 1.6rem;
            background: rgba(255,255,255,.6);
            backdrop-filter: blur(8px);
            color: var(--dark);
            font: inherit; font-size: .95rem; font-weight: 600;
            border: 1.5px solid rgba(61,140,135,.25);
            border-radius: 14px; cursor: pointer;
            text-decoration: none;
            transition: background 200ms, border-color 200ms;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,.9);
            border-color: var(--teal);
        }

        /* ── STATS BAR ── */
        .stats-bar {
            background: var(--dark);
            padding: 2.5rem 5vw;
            display: flex;
            justify-content: center;
            gap: 4rem;
            flex-wrap: wrap;
        }
        .stat-item {
            text-align: center;
            min-width: 120px;
        }
        .stat-number {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--teal-l);
            letter-spacing: -.02em;
            line-height: 1;
        }
        .stat-label {
            font-size: .78rem;
            font-weight: 600;
            color: rgba(168,216,213,.55);
            margin-top: .35rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        /* ── FEATURES ── */
        .features {
            padding: 5rem 5vw;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-eyebrow {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .72rem;
            font-weight: 700;
            color: var(--teal);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: .6rem;
        }
        .section-eyebrow-line {
            width: 28px; height: 2px;
            background: var(--teal);
            border-radius: 2px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 900;
            color: var(--dark);
            letter-spacing: -.03em;
            line-height: 1.15;
            margin-bottom: .6rem;
        }
        .section-sub {
            font-size: 1rem;
            color: #6b7280;
            font-weight: 500;
            max-width: 520px;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .feature-card {
            background: #fff;
            border: 1.5px solid #e8f0ef;
            border-radius: 20px;
            padding: 2rem 1.6rem;
            transition: transform 280ms cubic-bezier(.22,.68,0,1.2), box-shadow 280ms ease, border-color 280ms ease;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            opacity: 0;
            transition: opacity 280ms ease;
        }
        .feature-card:nth-child(1)::before { background: linear-gradient(90deg,#3d8c87,#2ac4b8); }
        .feature-card:nth-child(2)::before { background: linear-gradient(90deg,#3b82f6,#6366f1); }
        .feature-card:nth-child(3)::before { background: linear-gradient(90deg,#10b981,#34d399); }
        .feature-card:nth-child(4)::before { background: linear-gradient(90deg,#8b5cf6,#a78bfa); }
        .feature-card:nth-child(5)::before { background: linear-gradient(90deg,#f97316,#fbbf24); }
        .feature-card:nth-child(6)::before { background: linear-gradient(90deg,#ec4899,#f472b6); }
        .feature-card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 20px 52px rgba(14,38,34,.1);
            border-color: rgba(61,140,135,.15);
        }
        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
        }
        .feature-card:nth-child(1) .feature-icon { background: linear-gradient(135deg,#e8f5f4,#c8edea); color:#3d8c87; }
        .feature-card:nth-child(2) .feature-icon { background: linear-gradient(135deg,#eff6ff,#dbeafe); color:#3b82f6; }
        .feature-card:nth-child(3) .feature-icon { background: linear-gradient(135deg,#ecfdf5,#d1fae5); color:#10b981; }
        .feature-card:nth-child(4) .feature-icon { background: linear-gradient(135deg,#f5f3ff,#ede9fe); color:#8b5cf6; }
        .feature-card:nth-child(5) .feature-icon { background: linear-gradient(135deg,#fff7ed,#ffedd5); color:#f97316; }
        .feature-card:nth-child(6) .feature-icon { background: linear-gradient(135deg,#fdf2f8,#fce7f3); color:#ec4899; }

        .feature-card-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: .45rem;
        }
        .feature-card-desc {
            font-size: .88rem;
            color: #6b7280;
            line-height: 1.65;
        }

        /* ── HOW IT WORKS ── */
        .how-section {
            background: linear-gradient(180deg, #f0f7f6 0%, var(--bg) 100%);
            padding: 5rem 5vw;
        }
        .how-inner {
            max-width: 1100px;
            margin: 0 auto;
        }
        .how-steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.2rem;
            margin-top: 2.5rem;
            position: relative;
        }
        /* connector line */
        .how-steps::before {
            content: '';
            position: absolute;
            top: 50px;
            left: calc(12.5% + 25px);
            right: calc(12.5% + 25px);
            height: 2px;
            background: linear-gradient(90deg, var(--teal-l), var(--teal), var(--teal-l));
            border-radius: 2px;
            z-index: 0;
        }

        .how-step {
            text-align: center;
            position: relative;
            z-index: 1;
            background: #fff;
            border: 1.5px solid #e8f0ef;
            border-radius: 20px;
            padding: 1.8rem 1.2rem 1.4rem;
            transition: transform 250ms ease, box-shadow 250ms ease;
        }
        .how-step:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 40px rgba(61,140,135,.1);
        }
        .how-step-num {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--teal), var(--teal-d));
            color: #fff;
            font-size: .95rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.1rem;
            box-shadow: 0 4px 16px rgba(61,140,135,.35);
            position: relative;
            z-index: 1;
        }
        .how-step-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: var(--teal-s);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto .9rem;
            color: var(--teal);
        }
        .how-step-title {
            font-size: .95rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: .4rem;
        }
        .how-step-desc {
            font-size: .82rem;
            color: #6b7280;
            line-height: 1.55;
        }

        /* ── CTA SECTION ── */
        .cta-section {
            padding: 5rem 5vw;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .cta-card {
            max-width: 720px;
            margin: 0 auto;
            background: linear-gradient(135deg, var(--dark) 0%, #1a4a46 50%, var(--teal-d) 100%);
            border-radius: 28px;
            padding: 3.5rem 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(14,38,34,.3);
        }
        .cta-card::before {
            content:'';
            position:absolute;
            top:-80px; right:-80px;
            width:300px; height:300px;
            border-radius:50%;
            background:radial-gradient(circle, rgba(61,140,135,.3) 0%, transparent 70%);
        }
        .cta-card::after {
            content:'';
            position:absolute;
            bottom:-60px; left:-60px;
            width:200px; height:200px;
            border-radius:50%;
            background:radial-gradient(circle, rgba(168,216,213,.15) 0%, transparent 70%);
        }
        .cta-card > * { position: relative; z-index: 1; }

        .cta-title {
            font-size: 1.8rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: -.02em;
            margin-bottom: .6rem;
            line-height: 1.2;
        }
        .cta-sub {
            font-size: .95rem;
            color: rgba(168,216,213,.75);
            margin-bottom: 2rem;
            font-weight: 500;
            max-width: 440px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .95rem 2.4rem;
            background: #fff;
            color: var(--dark);
            font: inherit; font-size: 1rem; font-weight: 700;
            border: none; border-radius: 14px; cursor: pointer;
            text-decoration: none; letter-spacing: .01em;
            transition: transform 200ms, box-shadow 200ms;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }
        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,.2);
        }
        .btn-cta:active { transform: translateY(0); }

        /* ── FOOTER ── */
        footer {
            background: #fff;
            border-top: 1px solid #e8f0ef;
        }
        .footer-main {
            display: flex;
            justify-content: space-around;
            padding: 2.5rem 5vw;
            gap: 2rem;
            max-width: 1100px;
            margin: 0 auto;
        }
        .footer-col h4 {
            font-size: .9rem;
            font-weight: 800;
            color: var(--teal);
            margin-bottom: 1rem;
            letter-spacing: .02em;
        }
        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }
        .footer-col ul li a {
            font-size: .88rem;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            transition: color 150ms;
        }
        .footer-col ul li a:hover { color: var(--teal); }
        .footer-col ul li::before { content: '· '; color: var(--teal); font-weight: 700; }

        .footer-contact-list { list-style: none !important; display: flex; flex-direction: column; gap: .65rem; }
        .footer-contact-list li::before { content: '' !important; }
        .contact-item {
            display: flex; align-items: flex-start;
            gap: .55rem; font-size: .88rem;
            color: #374151; font-weight: 500;
        }
        .contact-icon {
            width: 26px; height: 26px; border-radius: 8px;
            background: var(--teal-s);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }
        .contact-icon svg { color: var(--teal); }

        .social-icons { display: flex; gap: .7rem; margin-top: .25rem; }
        .social-btn {
            width: 36px; height: 36px; border-radius: 10px;
            background: #f3f4f6;
            display: flex; align-items: center; justify-content: center;
            color: #374151; text-decoration: none;
            transition: background 200ms, color 200ms, transform 200ms;
        }
        .social-btn:hover { background: var(--teal); color: #fff; transform:translateY(-2px); }

        .footer-bar {
            background: #f9fafb;
            border-top: 1px solid #e8f0ef;
            padding: .9rem 5vw;
            display: flex; align-items: center; justify-content: space-between;
            font-size: .78rem; color: #9ca3af;
            flex-wrap: wrap; gap: .5rem;
        }
        .footer-bar a { color: #9ca3af; text-decoration: none; transition: color 150ms; }
        .footer-bar a:hover { color: var(--teal); }
        .footer-bar-links { display: flex; gap: 1rem; }
        .footer-bar-sep { color: #d1d5db; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .features-grid { grid-template-columns: 1fr 1fr; }
            .how-steps { grid-template-columns: 1fr 1fr; gap: 1.2rem; }
            .how-steps::before { display: none; }
            .hero-mockup { max-width: 340px; }
        }
        @media (max-width: 700px) {
            nav { padding: .8rem 1.2rem; }
            .nav-links { display: none; }
            .hero-inner { flex-direction: column; gap: 2rem; padding: 2rem 1.2rem; }
            .hero { min-height: 70vh; }
            .hero-mockup { max-width: 100%; }
            .hero-avec { font-size: 2.2rem; }
            .hero-tagline { font-size: 1.15rem; }
            .hero-actions { flex-direction: column; }
            .features-grid { grid-template-columns: 1fr; }
            .how-steps { grid-template-columns: 1fr; }
            .cta-card { padding: 2.5rem 1.5rem; }
            .cta-title { font-size: 1.35rem; }
            .footer-main { flex-direction: column; padding: 2rem 1.2rem; }
            .stats-bar { gap: 2rem; padding: 2rem 1.2rem; }
            .trust-strip { gap: 1.5rem; }
        }

        /* ── TRUST STRIP ── */
        .trust-strip {
            background: var(--bg);
            border-top: 1px solid #e8f0ef;
            border-bottom: 1px solid #e8f0ef;
            padding: 1.4rem 5vw;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2.5rem;
            flex-wrap: wrap;
        }
        .trust-label {
            font-size: .72rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .1em;
            white-space: nowrap;
        }
        .trust-items {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .82rem;
            font-weight: 700;
            color: #9ca3af;
        }
        .trust-item svg { opacity: .5; }

        /* ── SCROLL REVEAL ── */
        .reveal {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible { opacity: 1; transform: none; }
        .reveal-delay-1 { transition-delay: .1s; }
        .reveal-delay-2 { transition-delay: .18s; }
        .reveal-delay-3 { transition-delay: .26s; }
        .reveal-delay-4 { transition-delay: .34s; }
        .reveal-delay-5 { transition-delay: .42s; }

        /* ── SCROLL INDICATOR ── */
        .scroll-indicator {
            position: absolute;
            bottom: 2rem; left: 50%;
            transform: translateX(-50%);
            display: flex; flex-direction: column;
            align-items: center; gap: .4rem;
            z-index: 3; cursor: pointer;
        }
        .scroll-mouse {
            width: 22px; height: 36px;
            border: 2px solid rgba(255,255,255,.35);
            border-radius: 12px;
            position: relative;
        }
        .scroll-mouse::before {
            content: '';
            position: absolute;
            top: 5px; left: 50%;
            transform: translateX(-50%);
            width: 3px; height: 7px;
            border-radius: 2px;
            background: rgba(255,255,255,.6);
            animation: scrollPulse 2s ease-in-out infinite;
        }
        @keyframes scrollPulse {
            0%,100% { top:5px; opacity:1; }
            80%     { top:14px; opacity:0; }
        }
        .scroll-label {
            font-size: .62rem; font-weight: 700;
            color: rgba(255,255,255,.4);
            letter-spacing: .1em; text-transform: uppercase;
        }

        /* ── READING PROGRESS BAR ── */
        #reading-bar {
            position: fixed; top: 0; left: 0;
            height: 3px; width: 0%;
            background: linear-gradient(90deg, var(--teal), #2ac4b8);
            z-index: 200;
            border-radius: 0 2px 2px 0;
            transition: width .08s linear;
        }
    </style>
</head>
<body>

    {{-- ── NAVBAR ── --}}
    <nav>
        <a href="{{ url('/') }}" class="nav-brand">
            <img src="{{ asset('bagroung_logo.png') }}" alt="Witrack logo" class="nav-brand-logo">
            <div class="nav-brand-text">
                <span class="nav-brand-name">Witrack</span>
                <span class="nav-brand-sub">Links the Dots</span>
            </div>
        </a>

        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="{{ url('/') }}" class="active">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#how">Comment ça marche</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>

            <a href="{{ route('login') }}" class="nav-login-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Connexion
            </a>
        </div>
    </nav>

    {{-- ── HERO ── --}}
    <section class="hero">
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="hero-blob hero-blob-3"></div>
        <div class="hero-dots">
            <div class="hero-dot"></div><div class="hero-dot"></div>
            <div class="hero-dot"></div><div class="hero-dot"></div>
            <div class="hero-dot"></div><div class="hero-dot"></div>
            <div class="hero-dot"></div><div class="hero-dot"></div>
        </div>

        <div class="hero-inner">
            <div class="hero-illustration">
                {{-- Live chat mockup --}}
                <div class="hero-mockup">
                    <div class="mockup-topbar">
                        <div class="mockup-logo">
                            <img src="{{ asset('bagroung_logo.png') }}" alt="W">
                        </div>
                        <div>
                            <div class="mockup-title">Witrack AI</div>
                            <div class="mockup-status"><span class="mockup-online"></span> En ligne</div>
                        </div>
                        <div class="mockup-winbtns"><span></span><span></span><span></span></div>
                    </div>
                    <div class="mockup-body">
                        <div class="mockup-msg">
                            <div class="mockup-avatar bot">W</div>
                            <div>
                                <div class="mockup-bubble bot">Bonjour ! Que cherchez-vous dans vos documents&nbsp;?</div>
                            </div>
                        </div>
                        <div class="mockup-msg right">
                            <div class="mockup-avatar usr">V</div>
                            <div>
                                <div class="mockup-bubble usr">Quelle est la politique de congés&nbsp;?</div>
                            </div>
                        </div>
                        <div class="mockup-msg">
                            <div class="mockup-avatar bot">W</div>
                            <div>
                                <div class="mockup-bubble bot">
                                    Les employés bénéficient de <strong>25 jours</strong> de congés annuels payés selon le règlement interne.
                                    <div class="mockup-source">
                                        <span class="mockup-chip">📄 RH-Policy-2026.pdf</span>
                                        <span class="mockup-chip">p.12</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mockup-typing">
                            <div class="mockup-avatar bot">W</div>
                            <div class="mockup-typing-dots"><span></span><span></span><span></span></div>
                        </div>
                    </div>
                    <div class="mockup-input">
                        <div class="mockup-input-field">Posez votre question…</div>
                        <div class="mockup-send">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-content">
                <div class="hero-badge">
                    <div class="hero-badge-dot"></div>
                    Propulsé par l'IA
                </div>
                <p class="hero-avec">AVEC <span>Witrack</span></p>
                <p class="hero-tagline">Posez vos questions,<br>vos documents répondent.</p>
                <div class="hero-actions">
                    <a href="{{ route('login') }}" class="btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L9.1 9.1 2 12l7.1 2.9L12 22l2.9-7.1L22 12l-7.1-2.9z"/></svg>
                        Démarrer maintenant
                    </a>
                    <a href="#features" class="btn-secondary">
                        En savoir plus
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- scroll indicator --}}
        <div class="scroll-indicator">
            <div class="scroll-mouse"></div>
            <span class="scroll-label">Défiler</span>
        </div>
    </section>

    {{-- ── TRUST STRIP ── --}}
    <div class="trust-strip">
        <span class="trust-label">Construit avec</span>
        <div class="trust-items">
            <span class="trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                Python RAG
            </span>
            <span class="trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                FAISS Vector DB
            </span>
            <span class="trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                LLM Local
            </span>
            <span class="trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                100 % Privé
            </span>
            <span class="trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Laravel API
            </span>
        </div>
    </div>

    {{-- ── STATS BAR ── --}}
    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-number">RAG</div>
            <div class="stat-label">Technologie</div>
        </div>
        <div class="stat-item">
            <div class="stat-number" data-count="100" data-count-suffix="%">100%</div>
            <div class="stat-label">Privé &amp; sécurisé</div>
        </div>
        <div class="stat-item">
            <div class="stat-number" data-count="3" data-count-suffix="s">&lt;3s</div>
            <div class="stat-label">Temps de réponse</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">PDF · DOCX · TXT</div>
            <div class="stat-label">Documents supportés</div>
        </div>
    </div>

    {{-- ── FEATURES ── --}}
    <section class="features" id="features">
        <div class="section-eyebrow">
            <div class="section-eyebrow-line"></div>
            Fonctionnalités
        </div>
        <h2 class="section-title">Tout ce dont vous avez besoin</h2>
        <p class="section-sub">Une plateforme complète pour interroger, analyser et exploiter vos documents d'entreprise grâce à l'intelligence artificielle.</p>

        <div class="features-grid">
            {{-- 1 --}}
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <div class="feature-card-title">Recherche intelligente</div>
                <p class="feature-card-desc">Posez des questions en langage naturel et obtenez des réponses précises extraites de vos documents.</p>
            </div>
            {{-- 2 --}}
            <div class="feature-card reveal reveal-delay-1">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div class="feature-card-title">Multi-documents</div>
                <p class="feature-card-desc">Importez PDF, Word, TXT et plus encore. Witrack indexe et analyse tous vos fichiers automatiquement.</p>
            </div>
            {{-- 3 --}}
            <div class="feature-card reveal reveal-delay-2">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div class="feature-card-title">Sécurité totale</div>
                <p class="feature-card-desc">Vos données restent privées. Traitement local, aucune fuite vers des serveurs tiers.</p>
            </div>
            {{-- 4 --}}
            <div class="feature-card reveal reveal-delay-3">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="feature-card-title">Chat conversationnel</div>
                <p class="feature-card-desc">Dialoguez avec vos documents comme avec un collègue. Historique complet de vos échanges.</p>
            </div>
            {{-- 5 --}}
            <div class="feature-card reveal reveal-delay-4">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div class="feature-card-title">Réponses sourcées</div>
                <p class="feature-card-desc">Chaque réponse cite ses sources. Vérifiez et validez l'information en un clic.</p>
            </div>
            {{-- 6 --}}
            <div class="feature-card reveal reveal-delay-5">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="feature-card-title">Multi-utilisateurs</div>
                <p class="feature-card-desc">Gestion des rôles : admin, superviseur, employé. Chaque équipe accède à ses propres documents.</p>
            </div>
        </div>
    </section>

    {{-- ── HOW IT WORKS ── --}}
    <section class="how-section" id="how">
        <div class="how-inner">
            <div class="section-eyebrow">
                <div class="section-eyebrow-line"></div>
                Comment ça marche
            </div>
            <h2 class="section-title">Simple comme bonjour</h2>
            <p class="section-sub">En 4 étapes, transformez vos documents en une base de connaissances interrogeable.</p>

            <div class="how-steps">
                <div class="how-step reveal">
                    <div class="how-step-num">1</div>
                    <div class="how-step-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    </div>
                    <div class="how-step-title">Importez</div>
                    <p class="how-step-desc">Déposez vos documents PDF, Word ou texte dans la plateforme.</p>
                </div>
                <div class="how-step reveal reveal-delay-1">
                    <div class="how-step-num">2</div>
                    <div class="how-step-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </div>
                    <div class="how-step-title">Indexation IA</div>
                    <p class="how-step-desc">L'IA découpe, vectorise et indexe automatiquement le contenu.</p>
                </div>
                <div class="how-step reveal reveal-delay-2">
                    <div class="how-step-num">3</div>
                    <div class="how-step-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div class="how-step-title">Interrogez</div>
                    <p class="how-step-desc">Posez vos questions en langage naturel via le chat.</p>
                </div>
                <div class="how-step reveal reveal-delay-3">
                    <div class="how-step-num">4</div>
                    <div class="how-step-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div class="how-step-title">Réponse sourcée</div>
                    <p class="how-step-desc">Obtenez des réponses précises avec les références exactes.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── CTA SECTION ── --}}
    <section class="cta-section" id="about">
        <div class="cta-card">
            <h2 class="cta-title">Prêt à transformer vos documents<br>en connaissances&nbsp;?</h2>
            <p class="cta-sub">Rejoignez une nouvelle façon d'exploiter vos données. Analysez, interrogez et comprenez vos documents grâce à l'IA.</p>
            <a href="{{ route('login') }}" class="btn-cta">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L9.1 9.1 2 12l7.1 2.9L12 22l2.9-7.1L22 12l-7.1-2.9z"/></svg>
                Démarrer votre expérience
            </a>
        </div>
    </section>

    {{-- ── FOOTER ── --}}
    <div id="reading-bar"></div>
    <footer>
        <div class="footer-main">
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how" id="docs">Comment ça marche</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col" id="contact">
                <h4>Contact</h4>
                <ul class="footer-contact-list">
                    <li>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            contact@witrack.link
                        </div>
                    </li>
                    <li>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.6 3.46 2 2 0 0 1 3.56 1.28h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.71 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6.09 6.09l.99-.99a2 2 0 0 1 2.11-.45c.91.35 1.85.58 2.81.71A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            +213 560 51 85 86
                        </div>
                    </li>
                    <li>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <span>Centre d'Affaire ANPT Cyber Parc,<br>Sidi Abdellah, Rahmania, Alger</span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Witrack</h4>
                <div class="social-icons">
                    <a href="#" class="social-btn" aria-label="TikTok">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06z"/></svg>
                    </a>
                    <a href="#" class="social-btn" aria-label="Instagram">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="#" class="social-btn" aria-label="LinkedIn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer-bar">
            <span>Copyright &copy; 2026 Witrack. All rights reserved.</span>
            <div class="footer-bar-links">
                <a href="#">Privacy Policy</a>
                <span class="footer-bar-sep">|</span>
                <a href="#">Terms of Use</a>
                <span class="footer-bar-sep">|</span>
                <a href="#">Legal</a>
                <span class="footer-bar-sep">|</span>
                <a href="#">Site Map</a>
            </div>
            <span>Algeria</span>
        </div>
    </footer>

<script>
(function(){
    /* Reading progress bar */
    const bar = document.getElementById('reading-bar');
    const onScroll = () => {
        const h = document.documentElement;
        const pct = (h.scrollTop / (h.scrollHeight - h.clientHeight)) * 100;
        if (bar) bar.style.width = Math.min(pct, 100) + '%';
    };
    window.addEventListener('scroll', onScroll, { passive: true });

    /* Scroll reveal via IntersectionObserver */
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => io.observe(el));
    } else {
        document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
    }

    /* Animated stat counter */
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length) {
        const statsEl = document.querySelector('.stats-bar');
        const countIO = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                counters.forEach(el => {
                    const target = +el.dataset.count;
                    const suffix = el.dataset.countSuffix || '';
                    let cur = 0;
                    const step = target / 50;
                    const t = setInterval(() => {
                        cur = Math.min(cur + step, target);
                        el.textContent = Math.round(cur) + suffix;
                        if (cur >= target) clearInterval(t);
                    }, 25);
                });
                countIO.disconnect();
            }
        }, { threshold: 0.6 });
        if (statsEl) countIO.observe(statsEl);
    }

    /* Scroll indicator → smooth scroll to features */
    const si = document.querySelector('.scroll-indicator');
    if (si) si.addEventListener('click', () => {
        document.getElementById('features')?.scrollIntoView({ behavior: 'smooth' });
    });
})();
</script>
</body>
</html>
