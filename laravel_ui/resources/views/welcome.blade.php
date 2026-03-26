<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel UI') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f6f3ea;
            --ink: #1d1a14;
            --muted: #645f55;
            --card: #fffcf4;
            --accent: #d24f2f;
            --accent-2: #2f6f5e;
            --line: #d8d1c3;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Space Grotesk", system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 15%, #efe6d6 0, transparent 38%),
                radial-gradient(circle at 85% 82%, #e7d8b9 0, transparent 42%),
                var(--bg);
        }

        .layout {
            width: min(1120px, 92vw);
            margin: 2.5rem auto;
            display: grid;
            gap: 1.25rem;
            grid-template-columns: 1.2fr 0.8fr;
        }

        .hero {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 16px 40px rgba(35, 30, 19, 0.09);
            animation: rise 560ms ease-out both;
        }

        .label {
            display: inline-block;
            border-radius: 999px;
            border: 1px solid var(--line);
            padding: 0.35rem 0.8rem;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        h1 {
            font-family: "Fraunces", Georgia, serif;
            font-size: clamp(2rem, 4vw, 3.8rem);
            line-height: 1.05;
            margin: 1rem 0 0.9rem;
            max-width: 18ch;
        }

        .lead {
            max-width: 56ch;
            color: var(--muted);
            font-size: 1.05rem;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border-radius: 12px;
            padding: 0.72rem 1rem;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid transparent;
            transition: transform 160ms ease, background 160ms ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-secondary {
            border-color: var(--line);
            color: var(--ink);
            background: #fff;
        }

        .side {
            display: grid;
            gap: 1rem;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 1.2rem;
            box-shadow: 0 10px 24px rgba(35, 30, 19, 0.06);
            animation: rise 700ms ease-out both;
        }

        .card h2 {
            margin: 0;
            font-size: 1rem;
            letter-spacing: 0.01em;
        }

        .kpi {
            font-family: "Fraunces", Georgia, serif;
            font-size: 2rem;
            margin: 0.2rem 0 0;
            color: var(--accent-2);
        }

        .list {
            margin: 0.9rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 0.65rem;
            color: var(--muted);
        }

        .list li {
            display: flex;
            align-items: center;
            gap: 0.55rem;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--accent);
            flex: 0 0 auto;
        }

        .footer-note {
            width: min(1120px, 92vw);
            margin: 0 auto 2rem;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .ask-panel {
            margin-top: 1.5rem;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 0.9rem;
            background: #fff;
        }

        .ask-panel label {
            display: block;
            font-size: 0.88rem;
            color: var(--muted);
            margin-bottom: 0.45rem;
        }

        .ask-panel textarea {
            width: 100%;
            resize: vertical;
            min-height: 96px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 0.7rem;
            font: inherit;
            color: var(--ink);
        }

        .ask-row {
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .status {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .answer-card {
            margin-top: 1rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 0.8rem;
            background: #fff;
        }

        .answer-card h3 {
            margin: 0 0 0.45rem;
            font-size: 0.95rem;
        }

        .answer-text {
            white-space: pre-wrap;
            margin: 0;
            line-height: 1.5;
        }

        .sources {
            margin: 0.65rem 0 0;
            padding-left: 1rem;
            color: var(--muted);
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr;
                margin-top: 1.25rem;
            }

            .hero {
                padding: 1.5rem;
            }

            .kpi {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>
    <main class="layout">
        <section class="hero">
            <span class="label">Laravel UI Starter</span>
            <h1>Build faster with a clean Laravel interface.</h1>
            <p class="lead">
                This page is powered by Blade and plain CSS so it works immediately after install.
                Use it as your starting point for dashboards, landing pages, or internal tools.
            </p>

            <div class="actions">
                <a class="btn btn-primary" href="https://laravel.com/docs" target="_blank" rel="noreferrer">Read Docs</a>
                <a class="btn btn-secondary" href="{{ url('/up') }}">Health Check</a>
            </div>

            <div class="ask-panel">
                <label for="rag-question">Ask your RAG assistant</label>
                <textarea id="rag-question" placeholder="Ask a question about your indexed documents..."></textarea>
                <div class="ask-row">
                    <button id="ask-btn" class="btn btn-primary" type="button">Ask</button>
                    <span id="rag-status" class="status">Ready</span>
                </div>

                <div id="answer-card" class="answer-card" hidden>
                    <h3>Answer</h3>
                    <p id="answer-text" class="answer-text"></p>
                    <ol id="source-list" class="sources"></ol>
                </div>
            </div>
        </section>

        <aside class="side">
            <article class="card">
                <h2>Framework</h2>
                <p class="kpi">v{{ app()->version() }}</p>
            </article>

            <article class="card">
                <h2>Quick Setup</h2>
                <ul class="list">
                    <li><span class="dot"></span> Edit this view in resources/views/welcome.blade.php</li>
                    <li><span class="dot"></span> Add routes in routes/web.php</li>
                    <li><span class="dot"></span> Create controllers using php artisan make:controller</li>
                </ul>
            </article>
        </aside>
    </main>

    <p class="footer-note">Created on 26 mars 2026 for your Laravel UI request.</p>

    <script>
        const questionInput = document.getElementById('rag-question');
        const askButton = document.getElementById('ask-btn');
        const statusNode = document.getElementById('rag-status');
        const answerCard = document.getElementById('answer-card');
        const answerText = document.getElementById('answer-text');
        const sourceList = document.getElementById('source-list');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function askRag() {
            const question = questionInput.value.trim();
            if (!question) {
                statusNode.textContent = 'Please enter a question.';
                return;
            }

            askButton.disabled = true;
            statusNode.textContent = 'Searching and generating answer...';

            try {
                const response = await fetch('{{ route('rag.ask') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ question }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Request failed');
                }

                answerCard.hidden = false;
                answerText.textContent = data.answer || 'No answer returned.';
                sourceList.innerHTML = '';

                if (Array.isArray(data.sources) && data.sources.length > 0) {
                    data.sources.forEach((source) => {
                        const li = document.createElement('li');
                        li.textContent = source;
                        sourceList.appendChild(li);
                    });
                }

                statusNode.textContent = 'Done.';
            } catch (error) {
                answerCard.hidden = true;
                statusNode.textContent = error.message || 'Failed to get answer.';
            } finally {
                askButton.disabled = false;
            }
        }

        askButton.addEventListener('click', askRag);
        questionInput.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                askRag();
            }
        });
    </script>
</body>
</html>
