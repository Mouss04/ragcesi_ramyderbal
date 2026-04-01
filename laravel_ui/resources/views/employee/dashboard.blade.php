@php
    $isAdmin = in_array(auth()->user()?->role, ['admin', 'supervisor']);
    $layout  = $isAdmin ? 'layouts.admin' : 'layouts.employee';
@endphp
@extends($layout)

@push('styles')
<style>
/* ── Profile banner ── */
.emp-banner {
    background: linear-gradient(135deg, var(--teal) 0%, var(--teal-mid) 100%);
    border-radius: 18px;
    padding: 1.4rem 2rem;
    display: flex;
    align-items: center;
    gap: 1.2rem;
    margin-bottom: 1.4rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 28px var(--teal-shadow);
}
.emp-banner::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:200px; height:200px; border-radius:50%;
    background:rgba(255,255,255,0.07);
}
.emp-banner-avatar {
    width:54px; height:54px; border-radius:50%;
    background:rgba(255,255,255,0.18);
    border:2.5px solid rgba(255,255,255,0.4);
    display:grid; place-items:center; flex-shrink:0;
    overflow:hidden;
    font-size:1.2rem; font-weight:800; color:#fff;
}
.emp-banner-info { flex:1; z-index:1; }
.emp-banner-name { font-size:1.15rem; font-weight:800; color:#fff; }
.emp-banner-sub  { font-size:0.82rem; color:rgba(255,255,255,0.72); margin-top:3px; display:flex; align-items:center; gap:6px; }

/* ── Chat area ── */
.chat-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.4rem;
    align-items: start;
}
@media(max-width:900px) { .chat-layout { grid-template-columns: 1fr; } }

.chat-card {
    background: #fff;
    border: 1.5px solid var(--line);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 2px 14px var(--teal-shadow);
    display: flex;
    flex-direction: column;
    min-height: 520px;
}

.chat-head {
    padding: 1rem 1.4rem;
    border-bottom: 1.5px solid var(--line);
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.chat-head-icon {
    width:36px; height:36px; border-radius:10px;
    background: var(--teal-soft);
    display:grid; place-items:center;
}
.chat-head-title { font-size:0.95rem; font-weight:700; color:var(--text); }
.chat-head-sub   { font-size:0.78rem; color:var(--muted); }

.chat-messages {
    flex: 1;
    padding: 1.2rem 1.4rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    min-height: 320px;
    max-height: 420px;
    scrollbar-width: none;
}
.chat-messages::-webkit-scrollbar { display: none; }

.msg-bot, .msg-user {
    display: flex;
    gap: 0.5rem;
    align-items: flex-start;
}
.msg-user { flex-direction: row-reverse; }

.msg-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    display: grid; place-items: center;
    flex-shrink: 0;
    font-size: 0.68rem; font-weight: 800;
    overflow: hidden;
}
.msg-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.msg-avatar.bot { background: var(--teal-soft); color: var(--teal); }
.msg-avatar.user { background: var(--teal); color: #fff; }

.msg-bot > div:not(.msg-avatar), .msg-user > div:not(.msg-avatar) {
    display: flex;
    flex-direction: column;
    max-width: 75%;
    min-width: 0;
}

.msg-bubble {
    padding: 0.75rem 1rem;
    border-radius: 14px;
    font-size: 0.88rem;
    line-height: 1.6;
}
.msg-bubble.bot {
    background: var(--teal-soft);
    border: 1px solid var(--line);
    color: var(--text);
    border-top-left-radius: 4px;
}
.msg-bubble.user {
    background: linear-gradient(135deg, var(--teal), var(--teal-mid));
    color: #fff;
    border-top-right-radius: 4px;
}
.msg-bubble .sources-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--muted);
    margin-top: 0.6rem;
    margin-bottom: 0.3rem;
}
.msg-bubble .source-chip {
    display: inline-block;
    background: var(--teal-soft);
    color: var(--teal);
    border: 1px solid var(--line);
    border-radius: 6px;
    padding: 0.15rem 0.5rem;
    font-size: 0.72rem;
    font-weight: 600;
    margin: 0.15rem 0.15rem 0 0;
}
.msg-time {
    font-size: 0.68rem;
    color: var(--muted);
    margin-top: 0.3rem;
    text-align: right;
}
.msg-bot .msg-time { text-align: left; }

.chat-input-area {
    padding: 1rem 1.4rem;
    border-top: 1.5px solid var(--line);
    display: flex;
    gap: 0.75rem;
    align-items: flex-end;
}
.chat-input-area textarea {
    flex: 1;
    min-height: 44px;
    max-height: 120px;
    resize: none;
    border-radius: 12px;
    padding: 0.65rem 0.9rem;
    font-size: 0.88rem;
    border: 1.5px solid var(--line);
    outline: none;
    transition: border-color 140ms;
    line-height: 1.5;
    scrollbar-width: none;
}
.chat-input-area textarea::-webkit-scrollbar { display: none; }
.chat-input-area textarea:focus { border-color: var(--teal); }
.chat-send-btn {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--teal), var(--teal-mid));
    border: none;
    cursor: pointer;
    display: grid; place-items: center;
    flex-shrink: 0;
    transition: 140ms ease;
    box-shadow: 0 4px 12px var(--teal-shadow);
}
.chat-send-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px var(--teal-shadow); }
.chat-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* typing indicator */
.typing-indicator {
    display: none;
    align-items: center;
    gap: 4px;
    padding: 0.7rem 1rem;
    background: var(--teal-soft);
    border: 1px solid var(--line);
    border-radius: 14px;
    border-top-left-radius: 4px;
    width: fit-content;
}
.typing-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--teal);
    opacity: 0.4;
    animation: typingBounce 1.2s infinite;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-6px); opacity: 1; }
}

/* ── Right side panel ── */
.side-panel { display: flex; flex-direction: column; gap: 1rem; }

.tip-card {
    background: #fff;
    border: 1.5px solid var(--line);
    border-radius: 16px;
    padding: 1.1rem 1.3rem;
    box-shadow: 0 2px 10px var(--teal-shadow);
}
.tip-card-title {
    font-size: 0.85rem; font-weight: 700;
    color: var(--text); margin-bottom: 0.75rem;
    display: flex; align-items: center; gap: 0.5rem;
}
.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--line);
    cursor: pointer;
    transition: 140ms ease;
    border-radius: 6px;
}
.tip-item:last-child { border-bottom: none; }
.tip-item:hover { background: var(--teal-soft); padding-left: 0.4rem; }
.tip-icon {
    width: 28px; height: 28px; border-radius: 8px;
    background: var(--teal-soft); display: grid; place-items: center; flex-shrink: 0;
}
.tip-text { font-size: 0.82rem; color: var(--text); line-height: 1.4; }
.tip-sub  { font-size: 0.72rem; color: var(--muted); margin-top: 2px; }
</style>
@endpush

@section('content')

{{-- Banner --}}
<div class="emp-banner">
    <div class="emp-banner-avatar">
        @if(auth()->user()->avatar)
            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;">
        @else
            {{ strtoupper(substr(auth()->user()->name,0,2)) }}
        @endif
    </div>
    <div class="emp-banner-info">
        <div class="emp-banner-name">{{ __('Hello,') }} {{ auth()->user()->name }} </div>
        <div class="emp-banner-sub">
            @if(auth()->user()->location)
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ auth()->user()->location }} &bull;
            @endif
            {{ __('Ask a question to your company\'s AI') }}
        </div>
    </div>
    <div style="z-index:1;text-align:right;">
        <div id="emp-date" style="font-size:0.75rem;color:rgba(255,255,255,0.65);"></div>
    </div>
</div>

{{-- Main layout --}}
<div class="chat-layout">

    {{-- Chat card --}}
    <div class="chat-card">
        <div class="chat-head">
            <div class="chat-head-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
            </div>
            <div>
                <div class="chat-head-title">{{ __('AI Assistant') }}</div>
                <div class="chat-head-sub">{{ __('Query your company documents') }}</div>
            </div>
        </div>

        <div class="chat-messages" id="chat-messages">
            {{-- Welcome message --}}
            <div class="msg-bot" id="welcome-msg">
                <div class="msg-avatar bot">IA</div>
                <div>
                    <div class="msg-bubble bot">
                        {{ __('Hello! I am your AI assistant. Ask me any question about your company documents and I will answer as best I can.') }}
                    </div>
                    <div class="msg-time">Maintenant</div>
                </div>
            </div>
            {{-- Typing indicator --}}
            <div class="msg-bot" id="typing-row" style="display:none;">
                <div class="msg-avatar bot">IA</div>
                <div>
                    <div class="typing-indicator" id="typing-indicator" style="display:flex;">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-input-area">
            <textarea id="question-input" placeholder="{{ __('Ask your question here…') }}" rows="1"
                      onkeydown="handleKey(event)"></textarea>
            <button class="chat-send-btn" id="send-btn" onclick="sendQuestion()" title="Envoyer">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="side-panel">
        <div class="tip-card">
                <div class="tip-card-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ __('Latest conversations') }}
            </div>
            <div style="max-height:420px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:var(--line) transparent;">
            @forelse($recentSessions as $s)
                <a href="{{ route('employee.dashboard') }}?session_id={{ urlencode($s->session_key) }}"
                   style="text-decoration:none;color:inherit;display:block;">
                    <div class="tip-item">
                        <div class="tip-icon">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                        </div>
                        <div>
                            <div class="tip-text">{{ Str::limit($s->first_question, 50) }}</div>
                            <div class="tip-sub">{{ $s->messages_count }} msg · {{ $s->last_at->locale(app()->getLocale())->diffForHumans() }}</div>
                        </div>
                    </div>
                </a>
            @empty
                <div style="font-size:0.82rem;color:#9bb0b0;padding:0.4rem 0;">{{ __('No recent conversations.') }}</div>
            @endforelse
            </div>
        </div>
    </div>
</div>

{{-- New conversation button --}}
<div style="display:flex;justify-content:center;margin-top:2rem;margin-bottom:0.5rem;">
    <a href="{{ route('employee.dashboard') }}"
       style="display:inline-flex;align-items:center;gap:.7rem;padding:.85rem 2.5rem;border-radius:50px;background:var(--teal-dark);color:#fff;font-size:1rem;font-weight:700;text-decoration:none;box-shadow:0 4px 20px var(--teal-shadow);transition:160ms;letter-spacing:.01em;"
       onmouseover="this.style.background='var(--teal)';this.style.transform='translateY(-2px)';"
       onmouseout="this.style.background='var(--teal-dark)';this.style.transform='';">
        <span style="display:grid;place-items:center;width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,0.15);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </span>
        {{ __('New conversation') }}
    </a>
</div>

@endsection

@push('scripts')
<script>
// Unique session ID for this page load — all messages sent belong to one conversation
const SESSION_ID = (typeof crypto !== 'undefined' && crypto.randomUUID)
    ? crypto.randomUUID()
    : Date.now().toString(36) + Math.random().toString(36).substr(2);

const chatMessages = document.getElementById('chat-messages');
const questionInput = document.getElementById('question-input');
const sendBtn = document.getElementById('send-btn');
const typingRow = document.getElementById('typing-row');
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const _appLocale = '{{ app()->getLocale() === "fr" ? "fr-FR" : "en-GB" }}';
const _appTZ    = '{{ auth()->user()->timezone ?? "Africa/Algiers" }}';

(function updateEmpDate() {
    const el = document.getElementById('emp-date');
    if (el) {
        const d = new Date(new Date().toLocaleString('en-US', { timeZone: _appTZ }));
        el.textContent = d.toLocaleDateString(_appLocale, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }
    setTimeout(updateEmpDate, 60000);
})();

function now() {
    return new Date().toLocaleTimeString(_appLocale, { hour: '2-digit', minute: '2-digit' });
}

function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendQuestion();
    }
}

function fillQuestion(el) {
    const text = el.querySelector('.tip-text').textContent.trim();
    questionInput.value = text;
    questionInput.focus();
}

@php
    $userAvatarHtml = auth()->user()->avatar
        ? '<img src="' . Storage::url(auth()->user()->avatar) . '" alt="avatar">'
        : strtoupper(substr(auth()->user()->name, 0, 1));
@endphp
const USER_AVATAR_HTML = @json($userAvatarHtml);

function appendMsg(role, html, time) {
    const isBot = role === 'bot';
    const div = document.createElement('div');
    div.className = isBot ? 'msg-bot' : 'msg-user';
    div.innerHTML = `
        <div class="msg-avatar ${role}">${isBot ? 'IA' : USER_AVATAR_HTML}</div>
        <div>
            <div class="msg-bubble ${role}">${html}</div>
            <div class="msg-time">${time}</div>
        </div>
    `;
    chatMessages.insertBefore(div, typingRow);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

async function sendQuestion() {
    const question = questionInput.value.trim();
    if (!question) return;

    questionInput.value = '';
    sendBtn.disabled = true;

    const t = now();
    appendMsg('user', escHtml(question), t);

    // show typing
    typingRow.style.display = 'flex';
    chatMessages.scrollTop = chatMessages.scrollHeight;

    try {
        const res = await fetch('{{ route('rag.ask') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ question, chat_session_id: SESSION_ID }),
        });

        const data = await res.json();
        typingRow.style.display = 'none';

        if (data.error) {
            appendMsg('bot', `<span style="color:#dc2626;">Erreur : ${escHtml(data.error)}</span>`, now());
        } else {
            let html = escHtml(data.answer || '').replace(/\n/g, '<br>');
            if (data.sources && data.sources.length) {
                html += '<div class="sources-label">Sources</div>';
                data.sources.forEach(s => {
                    html += `<span class="source-chip">${escHtml(s)}</span>`;
                });
            }
            appendMsg('bot', html, now());
        }
    } catch (err) {
        typingRow.style.display = 'none';
        appendMsg('bot', '<span style="color:#dc2626;">Une erreur réseau est survenue.</span>', now());
    }

    sendBtn.disabled = false;
    questionInput.focus();
}

function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

// Auto-resize textarea
questionInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Pre-load a full session when session_id is passed in the URL
@if($viewMessages->isNotEmpty())
@php
    $preloadMessages = $viewMessages->map(function ($m) {
        return [
            'question' => $m->question,
            'answer'   => $m->answer ?? '',
            'sources'  => $m->sources ?? [],
            'time'     => $m->created_at->format('H:i'),
        ];
    })->values()->all();
@endphp
document.addEventListener('DOMContentLoaded', function() {
    const messages = @json($preloadMessages);

    const welcome = document.getElementById('welcome-msg');
    if (welcome) welcome.style.display = 'none';

    messages.forEach(function(m) {
        appendMsg('user', escHtml(m.question), m.time);
        if (m.answer) {
            let html = escHtml(m.answer).replace(/\n/g, '<br>');
            if (m.sources && m.sources.length) {
                html += '<div class="sources-label">Sources</div>';
                m.sources.forEach(function(s) {
                    html += '<span class="source-chip">' + escHtml(s) + '</span>';
                });
            }
            appendMsg('bot', html, m.time);
        }
    });

    chatMessages.scrollTop = chatMessages.scrollHeight;
});
@endif
</script>
@endpush
