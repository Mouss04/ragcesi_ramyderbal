@extends('layouts.app')

@section('content')
    <section class="panel">
        <style>
            .loading-wrap {
                display: none;
                margin-top: 0.4rem;
            }

            .loading-track {
                width: 100%;
                height: 10px;
                border-radius: 999px;
                background: #e6eeef;
                overflow: hidden;
                border: 1px solid #d4e1e3;
            }

            .loading-fill {
                width: 0;
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, #0f766e, #14b8a6);
                transition: width 220ms ease;
            }
        </style>

        <h1 class="title">Ask The Company AI</h1>
        <p class="subtitle">Type a question and get an answer generated from your indexed company documents.</p>

        <div class="grid cols-2" style="margin-top: 1rem; align-items: start;">
            <article class="card">
                <form id="ask-form" class="grid">
                    <div>
                        <label for="question">Your Question</label>
                        <textarea id="question" name="question" maxlength="1000" placeholder="Example: What are our onboarding steps?"></textarea>
                    </div>
                    <button id="ask-button" class="btn btn-primary" type="submit">Ask</button>
                    <div id="loading-wrap" class="loading-wrap" aria-live="polite">
                        <div class="loading-track">
                            <div id="loading-fill" class="loading-fill"></div>
                        </div>
                    </div>
                    <p id="ask-status" class="subtitle" style="margin: 0;"></p>
                </form>
            </article>

            <article class="card" id="answer-panel" style="display: none;">
                <h2 style="margin-top: 0;">AI Answer</h2>
                <p id="answer-text" style="white-space: pre-wrap;"></p>
                <h3>Sources</h3>
                <ul id="answer-sources"></ul>
            </article>
        </div>
    </section>

    <script>
        const form = document.getElementById('ask-form');
        const statusEl = document.getElementById('ask-status');
        const answerPanel = document.getElementById('answer-panel');
        const answerText = document.getElementById('answer-text');
        const answerSources = document.getElementById('answer-sources');
        const askButton = document.getElementById('ask-button');
        const loadingWrap = document.getElementById('loading-wrap');
        const loadingFill = document.getElementById('loading-fill');

        let loadingTimer = null;

        function startLoading() {
            let progress = 10;
            loadingWrap.style.display = 'block';
            loadingFill.style.width = progress + '%';

            loadingTimer = window.setInterval(function () {
                if (progress < 92) {
                    progress += Math.max(1, (95 - progress) * 0.08);
                    loadingFill.style.width = Math.min(progress, 92) + '%';
                }
            }, 250);
        }

        function finishLoading() {
            if (loadingTimer) {
                window.clearInterval(loadingTimer);
                loadingTimer = null;
            }

            loadingFill.style.width = '100%';
            window.setTimeout(function () {
                loadingWrap.style.display = 'none';
                loadingFill.style.width = '0%';
            }, 220);
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const question = document.getElementById('question').value.trim();
            if (!question) {
                statusEl.textContent = 'Please enter a question.';
                return;
            }

            statusEl.textContent = 'Asking AI...';
            askButton.disabled = true;
            answerPanel.style.display = 'none';
            answerSources.innerHTML = '';
            startLoading();

            try {
                const response = await fetch('{{ route('rag.ask') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ question })
                });

                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.error || 'Unknown error');
                }

                answerText.textContent = payload.answer || 'No answer returned.';
                const sources = payload.sources || [];

                if (sources.length === 0) {
                    const li = document.createElement('li');
                    li.textContent = 'No sources returned.';
                    answerSources.appendChild(li);
                } else {
                    sources.forEach(function (source) {
                        const li = document.createElement('li');
                        li.textContent = source;
                        answerSources.appendChild(li);
                    });
                }

                answerPanel.style.display = 'block';
                statusEl.textContent = 'Answer ready.';
            } catch (error) {
                statusEl.textContent = 'Error: ' + error.message;
            } finally {
                finishLoading();
                askButton.disabled = false;
            }
        });
    </script>
@endsection
