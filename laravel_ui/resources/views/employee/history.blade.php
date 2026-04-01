@php $layout = in_array(auth()->user()?->role, ['admin','supervisor']) ? 'layouts.admin' : 'layouts.employee'; @endphp
@extends($layout)

@push('styles')
<style>
/* ── Hero ── */
.hist-hero {
    background: linear-gradient(135deg, var(--teal) 0%, var(--teal-mid) 100%);
    border-radius: 18px;
    padding: 1.5rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 28px var(--teal-shadow);
    margin-bottom: 1.6rem;
}
.hist-hero::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:200px; height:200px; border-radius:50%;
    background:rgba(255,255,255,.07);
}
.hist-hero-left { display:flex; align-items:center; gap:1.1rem; z-index:1; }
.hist-hero-icon {
    width:54px; height:54px; border-radius:15px;
    background:rgba(255,255,255,.15);
    border:1.5px solid rgba(255,255,255,.25);
    display:grid; place-items:center; flex-shrink:0;
}
.hist-hero-title { font-size:1.3rem; font-weight:800; color:#fff; }
.hist-hero-sub   { font-size:.82rem; color:rgba(255,255,255,.72); margin-top:3px; }
.hist-count-pill {
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.28);
    border-radius:12px;
    padding:.5rem 1.1rem;
    text-align:center; z-index:1;
}
.hist-count-num   { font-size:1.6rem; font-weight:800; color:#fff; }
.hist-count-label { font-size:.72rem; color:rgba(255,255,255,.7); font-weight:600; }

/* ── History list ── */
.hist-card {
    background: var(--white);
    border: 1.5px solid var(--line);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px var(--teal-shadow);
}
.hist-card-head {
    padding: 0.9rem 1.4rem;
    border-bottom: 1.5px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.8rem;
}
.hist-card-head-title { font-size:0.9rem; font-weight:700; color:var(--text); }

.hist-item {
    border-bottom: 1px solid var(--line);
    padding: 1.1rem 1.4rem;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 1rem;
    align-items: start;
    transition: background 140ms;
}
.hist-item:last-child { border-bottom: none; }
.hist-item:hover { background: var(--teal-soft); }

.hist-item-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: var(--teal-soft);
    display: grid; place-items: center; flex-shrink: 0;
    margin-top: 2px;
}
.hist-item-q {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.35rem;
    line-height: 1.4;
}
.hist-item-a {
    font-size: 0.82rem;
    color: var(--muted);
    line-height: 1.55;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.hist-item-meta {
    font-size: 0.72rem;
    color: var(--muted);
    white-space: nowrap;
    text-align: right;
}
.hist-item-reask {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.65rem;
    border-radius: 7px;
    border: 1px solid var(--line);
    background: var(--white);
    font: inherit;
    font-size: 0.76rem;
    font-weight: 600;
    color: var(--teal);
    cursor: pointer;
    margin-top: 0.5rem;
    transition: 140ms ease;
    text-decoration: none;
}
.hist-item-reask:hover { background: var(--teal-soft); border-color: var(--teal); }

/* ── Empty ── */
.empty-state {
    text-align:center;
    padding:3rem 1rem;
    color:var(--muted);
    font-size:0.9rem;
}
.empty-state svg { margin-bottom:1rem; opacity:.45; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="hist-hero">
    <div class="hist-hero-left">
        <div class="hist-hero-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div>
            <div class="hist-hero-title">{{ __('Question history') }}</div>
            <div class="hist-hero-sub">{{ __('Find all your questions asked to the AI assistant') }}</div>
        </div>
    </div>
    <div class="hist-count-pill">
        <div class="hist-count-num">{{ $sessions->total() }}</div>
        <div class="hist-count-label">{{ __('Conversations') }}</div>
    </div>
</div>

<div class="hist-card">
    <div class="hist-card-head">
        <div class="hist-card-head-title">{{ __('Your conversations') }}</div>
        @if($sessions->total() > 0)
            <form method="POST" action="{{ route('employee.history.clear') }}"
                  onsubmit="return confirm('Vider tout l\'historique ?');">
                @csrf @method('DELETE')
                <button type="submit"
                    style="display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .8rem;border-radius:8px;border:1px solid #fecaca;background:#fff;font:inherit;font-size:.8rem;font-weight:600;color:#dc2626;cursor:pointer;transition:140ms;"
                    onmouseover="this.style.background='#fef2f2';"
                    onmouseout="this.style.background='#fff';">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                    </svg>
                    Vider l'historique
                </button>
            </form>
        @endif
    </div>

    @forelse($sessions as $s)
        <div class="hist-item">
            <div class="hist-item-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
            </div>
            <div style="flex:1;">
                <div class="hist-item-q">{{ $s->first_question }}</div>
                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.3rem;">
                    <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.72rem;font-weight:600;color:var(--teal);background:var(--teal-soft);border:1px solid var(--line);border-radius:6px;padding:.1rem .45rem;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                        {{ $s->messages_count }} message{{ $s->messages_count > 1 ? 's' : '' }}
                    </span>
                </div>
                <a href="{{ route('employee.dashboard') }}?session_id={{ urlencode($s->session_key) }}"
                   class="hist-item-reask">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{ __('View conversation') }}
                </a>
            </div>
            <div class="hist-item-meta">
                {{ $s->last_at->locale('fr')->isoFormat('D MMM') }}<br>
                {{ $s->last_at->format('H:i') }}
            </div>
        </div>
    @empty
        <div class="empty-state">
            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <p>{{ __('You have not asked any questions yet.') }}</p>
            <p style="margin-top:.5rem;"><a href="{{ route('employee.dashboard') }}" style="color:var(--teal);font-weight:600;">{{ __('Ask my first question') }} →</a></p>
        </div>
    @endforelse
</div>

@if($sessions->hasPages())
    <div style="display:flex;justify-content:center;margin-top:1.2rem;">
        {{ $sessions->links() }}
    </div>
@endif

@endsection
