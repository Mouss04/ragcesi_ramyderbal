@extends('layouts.admin')

@push('styles')
<style>
/* ═══════════════════════════════
   SYSTÈME RAG — ADMIN INTERFACE
═══════════════════════════════ */

.rag-layout {
    display: grid;
    grid-template-rows: auto 1fr auto;
    height: calc(100vh - 64px - 3rem); /* viewport minus topbar & padding */
    gap: 1.2rem;
}

/* ── Hero header ── */
.rag-hero {
    background: linear-gradient(135deg, var(--teal) 0%, var(--teal-mid) 55%, var(--teal-light) 100%);
    border-radius: 18px;
    padding: 1.4rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 28px var(--teal-shadow);
    flex-shrink: 0;
}
.rag-hero::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:200px; height:200px; border-radius:50%;
    background:rgba(255,255,255,.07);
}
.rag-hero-icon {
    width:52px; height:52px; border-radius:14px;
    background:rgba(255,255,255,.15);
    border:1.5px solid rgba(255,255,255,.25);
    display:grid; place-items:center; flex-shrink:0;
}
.rag-hero-title { font-size:1.25rem; font-weight:800; color:#fff; }
.rag-hero-sub   { font-size:.82rem; color:rgba(255,255,255,.7); margin-top:3px; }
.rag-status-badge {
    display:inline-flex; align-items:center; gap:.5rem;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
    border-radius:20px; padding:.3rem .9rem;
    font-size:.78rem; font-weight:600; color:rgba(255,255,255,.9);
}
.rag-status-dot {
    width:7px; height:7px; border-radius:50%;
    background:#4ade80;
    box-shadow: 0 0 6px #4ade80;
    animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
    0%,100% { opacity:1; }
    50% { opacity:.4; }
}

/* ── Chat container ── */
.rag-chat {
    background:#fff;
    border:1.5px solid var(--line);
    border-radius:18px;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    box-shadow:0 2px 12px var(--teal-shadow);
    min-height:0;
}

/* ── Loading bar ── */
.rag-progress-wrap {
    height:3px;
    background:#edf2f2;
    overflow:hidden;
    flex-shrink:0;
    opacity:0;
    transition:opacity 200ms;
}
.rag-progress-wrap.active { opacity:1; }
.rag-progress-bar {
    height:100%;
    width:0%;
    background:linear-gradient(90deg, var(--teal), var(--teal-mid), var(--teal-light));
    border-radius:0 3px 3px 0;
    transition:width 200ms ease;
    animation: none;
}
.rag-progress-wrap.active .rag-progress-bar {
    animation: progress-indeterminate 1.6s ease-in-out infinite;
}
@keyframes progress-indeterminate {
    0%   { width:0%;   margin-left:0%; }
    50%  { width:60%;  margin-left:20%; }
    100% { width:0%;   margin-left:100%; }
}

/* messages scroll area */
.rag-messages {
    flex:1;
    overflow-y:auto;
    padding:1.5rem 1.6rem;
    display:flex;
    flex-direction:column;
    gap:1.2rem;
    scroll-behavior:smooth;
}
.rag-messages::-webkit-scrollbar { width:5px; }
.rag-messages::-webkit-scrollbar-track { background:transparent; }
.rag-messages::-webkit-scrollbar-thumb { background:#d0e2e2; border-radius:99px; }

/* empty state */
.rag-empty {
    flex:1;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:1rem;
    color:#9bb0b0;
    padding:2rem;
    text-align:center;
}
.rag-empty-icon {
    width:72px; height:72px; border-radius:20px;
    background:var(--teal-soft);
    display:grid; place-items:center;
}
.rag-empty-title { font-size:1rem; font-weight:700; color:#4a6666; }
.rag-empty-sub   { font-size:.82rem; max-width:320px; line-height:1.5; }

/* suggestion chips */
.rag-suggestions {
    display:flex; flex-wrap:wrap; gap:.5rem;
    justify-content:center; margin-top:.5rem;
}
.suggestion-chip {
    background:var(--teal-soft); border:1.5px solid #c8e8e8;
    border-radius:20px; padding:.35rem .9rem;
    font-size:.78rem; font-weight:600; color:var(--teal);
    cursor:pointer; transition:background 140ms, border-color 140ms;
}
.suggestion-chip:hover { background:#d5f0f0; border-color:var(--teal); }

/* ── Message bubbles ── */
.msg { display:flex; gap:.9rem; align-items:flex-start; }
.msg.user { flex-direction:row-reverse; }

.msg-avatar {
    width:36px; height:36px; border-radius:50%;
    display:grid; place-items:center; flex-shrink:0;
    font-size:.78rem; font-weight:800;
}
.msg.user .msg-avatar {
    background:linear-gradient(135deg, var(--teal), var(--teal-mid));
    color:#fff;
    overflow:hidden;
}
.msg.user .msg-avatar img { width:100%; height:100%; object-fit:cover; }
.msg.bot  .msg-avatar {
    background:var(--teal-soft);
    color:var(--teal);
}

.msg-bubble {
    max-width:72%;
    border-radius:16px;
    padding:.85rem 1.1rem;
    font-size:.88rem;
    line-height:1.65;
    position:relative;
}
.msg.user .msg-bubble {
    background:linear-gradient(135deg, var(--teal), var(--teal-mid));
    color:#fff;
    border-bottom-right-radius:4px;
}
.msg.bot .msg-bubble {
    background:#f6fafa;
    border:1.5px solid #e4efef;
    color:#1e2c2c;
    border-bottom-left-radius:4px;
}

.msg-time {
    font-size:.68rem;
    margin-top:.4rem;
    opacity:.55;
}
.msg.user .msg-time { text-align:right; }

/* Sources */
.msg-sources {
    margin-top:.6rem;
    display:flex;
    flex-wrap:wrap;
    gap:.35rem;
}
.source-tag {
    display:inline-flex; align-items:center; gap:.3rem;
    background:#fff; border:1.5px solid #d0e2e2;
    border-radius:6px; padding:.2rem .55rem;
    font-size:.72rem; font-weight:600; color:#4a7070;
}

/* Typing indicator */
.msg-typing .msg-bubble {
    background:#f6fafa;
    border:1.5px solid #e4efef;
    padding:.9rem 1.2rem;
}
.typing-dots { display:flex; gap:5px; align-items:center; }
.typing-dots span {
    width:7px; height:7px; border-radius:50%;
    background:var(--teal); opacity:.4;
    animation: bounce-dot .9s infinite;
}
.typing-dots span:nth-child(2) { animation-delay:.18s; }
.typing-dots span:nth-child(3) { animation-delay:.36s; }
@keyframes bounce-dot {
    0%,80%,100% { transform:translateY(0); opacity:.35; }
    40% { transform:translateY(-6px); opacity:1; }
}

/* ── Input bar ── */
.rag-input-bar {
    border-top:1.5px solid #edf2f2;
    padding:1rem 1.4rem;
    background:#fff;
    display:flex;
    align-items:flex-end;
    gap:.8rem;
}
.rag-textarea-wrap { flex:1; position:relative; }
#rag-question {
    width:100%;
    border:1.5px solid #d0e2e2;
    border-radius:14px;
    padding:.7rem 1rem;
    font:inherit; font-size:.9rem;
    color:#1e2c2c; background:#fafefe;
    resize:none; outline:none;
    min-height:48px; max-height:160px;
    overflow-y:auto;
    line-height:1.5;
    transition:border-color 150ms, box-shadow 150ms;
}
#rag-question:focus {
    border-color:var(--teal);
    box-shadow:0 0 0 3px var(--teal-shadow);
    background:#fff;
}
.rag-send-btn {
    width:48px; height:48px; border-radius:13px;
    background:linear-gradient(135deg, var(--teal), var(--teal-mid));
    border:none; cursor:pointer;
    display:grid; place-items:center;
    flex-shrink:0;
    box-shadow:0 3px 10px var(--teal-shadow);
    transition:opacity 150ms, transform 120ms;
}
.rag-send-btn:hover  { opacity:.9; transform:translateY(-1px); }
.rag-send-btn:active { transform:translateY(0); }
.rag-send-btn:disabled { opacity:.45; cursor:not-allowed; transform:none; }

.rag-shortcut {
    font-size:.72rem; color:#9bb0b0; text-align:center; margin-top:.4rem;
}
</style>
@endpush

@section('content')

<div class="rag-layout">

    {{-- ── Hero ── --}}
    <div class="rag-hero">
        <div style="display:flex;align-items:center;gap:1rem;z-index:1;">
            <div class="rag-hero-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    <path d="M11 8v6M8 11h6"/>
                </svg>
            </div>
            <div>
                <div class="rag-hero-title">Système RAG</div>
                <div class="rag-hero-sub">Posez vos questions — l'IA consulte la base documentaire</div>
            </div>
        </div>
        <div style="z-index:1;">
            <div class="rag-status-badge">
                <div class="rag-status-dot"></div>
                Pipeline actif
            </div>
        </div>
    </div>

    {{-- ── Chat ── --}}
    <div class="rag-chat">
        <div class="rag-messages" id="rag-messages">

            {{-- Empty state --}}
            <div class="rag-empty" id="rag-empty">
                <div class="rag-empty-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                </div>
                <div class="rag-empty-title">Interrogez vos documents</div>
                <div class="rag-empty-sub">L'IA analyse la base documentaire indexée et vous répond en langage naturel.</div>
                <div class="rag-suggestions" id="suggestions">
                    <span class="suggestion-chip" onclick="useSuggestion(this)">Quels sont les projets en cours ?</span>
                    <span class="suggestion-chip" onclick="useSuggestion(this)">Résume les documents disponibles</span>
                    <span class="suggestion-chip" onclick="useSuggestion(this)">Quelles sont les procédures RH ?</span>
                    <span class="suggestion-chip" onclick="useSuggestion(this)">Donne-moi un résumé exécutif</span>
                </div>
            </div>

        </div>

        {{-- Loading bar --}}
        <div class="rag-progress-wrap" id="rag-progress">
            <div class="rag-progress-bar" id="rag-progress-bar"></div>
        </div>

        {{-- Input bar --}}
        <div class="rag-input-bar">
            <div class="rag-textarea-wrap">
                <textarea id="rag-question" placeholder="Posez votre question à l'IA…" rows="1"
                          onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
            </div>
            <button class="rag-send-btn" id="send-btn" onclick="sendQuestion()" title="Envoyer (Entrée)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
        <div class="rag-shortcut" style="padding-bottom:.65rem;">
            Appuyez sur <kbd style="background:#f0f4f4;border:1px solid #d0e2e2;border-radius:4px;padding:.1rem .35rem;font-size:.7rem;">Entrée</kbd> pour envoyer &nbsp;·&nbsp;
            <kbd style="background:#f0f4f4;border:1px solid #d0e2e2;border-radius:4px;padding:.1rem .35rem;font-size:.7rem;">Maj+Entrée</kbd> pour saut de ligne
        </div>
    </div>

</div>

@push('scripts')
<script>
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const ASK_URL = '{{ route("admin.rag.ask") }}';

/* avatar initials / image */
@php
    $avatarHtml = auth()->user()->avatar
        ? '<img src="' . Storage::url(auth()->user()->avatar) . '" alt="avatar" style="width:100%;height:100%;object-fit:cover;">'
        : '<span>' . strtoupper(substr(auth()->user()->name, 0, 2)) . '</span>';
@endphp
const USER_AVATAR_HTML = @json($avatarHtml);

function now12() {
    return new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 160) + 'px';
}

function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendQuestion();
    }
}

function useSuggestion(el) {
    document.getElementById('rag-question').value = el.textContent;
    autoResize(document.getElementById('rag-question'));
    sendQuestion();
}

function appendMsg(role, html, sources) {
    const empty = document.getElementById('rag-empty');
    if (empty) empty.remove();

    const container = document.getElementById('rag-messages');
    const wrap = document.createElement('div');
    wrap.className = 'msg ' + role;

    let avatarInner = role === 'user'
        ? USER_AVATAR_HTML
        : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>`;

    let sourcesHtml = '';
    if (sources && sources.length) {
        sourcesHtml = '<div class="msg-sources">' +
            sources.map(s => `<span class="source-tag">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                ${s.split('/').pop()}
            </span>`).join('') +
            '</div>';
    }

    wrap.innerHTML = `
        <div class="msg-avatar">${avatarInner}</div>
        <div>
            <div class="msg-bubble">${html}${sourcesHtml}</div>
            <div class="msg-time">${now12()}</div>
        </div>`;

    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;
    return wrap;
}

function appendTyping() {
    const empty = document.getElementById('rag-empty');
    if (empty) empty.remove();

    const container = document.getElementById('rag-messages');
    const wrap = document.createElement('div');
    wrap.className = 'msg bot msg-typing';
    wrap.id = 'typing-indicator';
    wrap.innerHTML = `
        <div class="msg-avatar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        <div>
            <div class="msg-bubble">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>`;
    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;
}

function removeTyping() {
    const t = document.getElementById('typing-indicator');
    if (t) t.remove();
}

function showProgress() {
    document.getElementById('rag-progress').classList.add('active');
}
function hideProgress() {
    const wrap = document.getElementById('rag-progress');
    wrap.classList.remove('active');
}

function escapeHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
             .replace(/\n/g,'<br>');
}

async function sendQuestion() {
    const ta  = document.getElementById('rag-question');
    const btn = document.getElementById('send-btn');
    const q   = ta.value.trim();
    if (!q) return;

    ta.value = '';
    ta.style.height = 'auto';
    btn.disabled = true;

    appendMsg('user', escapeHtml(q), null);
    appendTyping();
    showProgress();

    try {
        const res  = await fetch(ASK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ question: q }),
        });

        const data = await res.json();
        removeTyping();
        hideProgress();

        if (!res.ok || data.error) {
            appendMsg('bot',
                `<span style="color:#dc2626;">⚠ ${escapeHtml(data.error || 'Erreur inconnue.')}</span>`,
                null);
        } else {
            appendMsg('bot', escapeHtml(data.answer || '(Aucune réponse)'), data.sources || []);
        }
    } catch (err) {
        removeTyping();
        hideProgress();
        appendMsg('bot',
            `<span style="color:#dc2626;">⚠ Impossible de contacter le pipeline RAG.</span>`,
            null);
    } finally {
        btn.disabled = false;
        ta.focus();
    }
}
</script>
@endpush

@endsection
