@extends('layouts.admin')

@push('styles')
<style>
    /* ── Profile banner ── */
    .profile-banner {
        background: linear-gradient(135deg, var(--teal) 0%, var(--teal-mid) 58%, var(--teal-light) 100%);
        border-radius: 18px;
        padding: 1.6rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 1.4rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px var(--teal-shadow);
    }
    .profile-banner::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.07);
    }
    .profile-banner::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 80px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }
    .profile-avatar {
        width: 62px; height: 62px;
        border-radius: 50%;
        background: rgba(255,255,255,0.18);
        border: 2.5px solid rgba(255,255,255,0.4);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .profile-name  { font-size: 1.2rem; font-weight: 800; color: #fff; }
    .profile-role  { font-size: 0.82rem; color: rgba(255,255,255,0.75); margin-top: 2px; }
    .profile-loc   { display:flex; align-items:center; gap:4px; font-size:0.78rem; color:rgba(255,255,255,0.65); margin-top:5px; }
    .profile-date  { font-size: 0.82rem; color: rgba(255,255,255,0.75); text-align:right; white-space:nowrap; z-index:1; }

    /* ── Stat cards ── */
    .stat-card {
        background: #fff;
        border: 1.5px solid #dde8e8;
        border-radius: 16px;
        padding: 1.3rem 1.4rem;
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
        position: relative;
        overflow: hidden;
        transition: box-shadow 180ms, transform 180ms;
    }
    .stat-card:hover {
        box-shadow: 0 8px 24px var(--teal-shadow);
        transform: translateY(-2px);
    }
    .stat-card-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .stat-card-top { display:flex; align-items:center; justify-content:space-between; }
    .stat-card-label { font-size: 0.82rem; font-weight: 600; color: #6b8080; }
    .stat-card-value { font-size: 2.4rem; font-weight: 800; color: #1e2c2c; line-height: 1; }
    .stat-card-footer { font-size: 0.75rem; color: #6b8080; display:flex; align-items:center; gap:4px; }
    .stat-card-stripe {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 4px;
        border-radius: 0 0 16px 16px;
    }

    /* ── Quick actions ── */
    .actions-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 1.2rem;
    }
    .action-card {
        background: #fff;
        border: 1.5px solid #dde8e8;
        border-radius: 14px;
        padding: 1.1rem 1.3rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: #1e2c2c;
        transition: border-color 160ms, box-shadow 160ms, transform 160ms;
    }
    .action-card:hover {
        border-color: var(--teal);
        box-shadow: 0 6px 20px var(--teal-shadow);
        transform: translateY(-2px);
        color: var(--teal);
    }
    .action-icon {
        width: 42px; height: 42px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .action-label { font-weight: 700; font-size: 0.9rem; }
    .action-sub   { font-size: 0.76rem; color: #6b8080; margin-top: 2px; }
    .action-arrow { margin-left: auto; opacity: 0.35; }
    .action-card:hover .action-arrow { opacity: 1; }
</style>
@endpush

@section('content')

    {{-- Profile banner --}}
    <div class="profile-banner">
        <div style="display:flex;align-items:center;gap:1.1rem;z-index:1;">
            <div class="profile-avatar" style="@if(auth()->user()->avatar) padding:0;overflow:hidden; @endif">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:1.4rem;font-weight:800;color:rgba(255,255,255,.9);">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</span>
                @endif
            </div>
            <div>
                <div class="profile-name">{{ auth()->user()->name }}</div>
                <div class="profile-role">{{ ucfirst(auth()->user()->role) }}</div>
                <div class="profile-loc">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="rgba(255,255,255,0.7)" stroke="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    {{ last(explode('/', auth()->user()->timezone ?? 'Africa/Algiers')) }}
                </div>
            </div>
        </div>
        <div class="profile-date">
            <span id="dash-date"></span><br>
            <span id="dash-time" style="font-size:1.1rem;font-weight:700;color:#fff;"></span>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid cols-3">

        {{-- Users --}}
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-card-label">Nombre d'utilisateurs</span>
                <div class="stat-card-icon" style="background:var(--teal-soft);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value">{{ $usersCount }}</div>
            <div class="stat-card-footer">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" style="stroke:var(--teal)" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                Utilisateurs enregistrés
            </div>
            <div class="stat-card-stripe" style="background:linear-gradient(90deg,var(--teal),var(--teal-mid));"></div>
        </div>

        {{-- Documents --}}
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-card-label">Nombre de documents</span>
                <div class="stat-card-icon" style="background:#fef9ec;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d4a017" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value">{{ $documentsCount }}</div>
            <div class="stat-card-footer">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#d4a017" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                Documents indexés
            </div>
            <div class="stat-card-stripe" style="background:linear-gradient(90deg,#d4a017,#f5cb5c);"></div>
        </div>

        {{-- Queries today --}}
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-card-label">Requêtes aujourd'hui</span>
                <div class="stat-card-icon" style="background:#f0f0ff;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value">{{ $todayQueriesCount }}</div>
            <div class="stat-card-footer">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                Requêtes RAG du jour
            </div>
            <div class="stat-card-stripe" style="background:linear-gradient(90deg,#6366f1,#a5b4fc);"></div>
        </div>

    </div>

    {{-- Quick actions --}}
    <div class="actions-row">
        <a href="{{ route('admin.users.index') }}" class="action-card">
            <div class="action-icon" style="background:var(--teal-soft);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div>
                <div class="action-label">Gestion des utilisateurs</div>
                <div class="action-sub">Créer, modifier, supprimer des comptes</div>
            </div>
            <svg class="action-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>

        <a href="{{ route('admin.documents.index') }}" class="action-card">
            <div class="action-icon" style="background:#fef9ec;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d4a017" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <div>
                <div class="action-label">Gestion des documents</div>
                <div class="action-sub">Importer et indexer des fichiers PDF</div>
            </div>
            <svg class="action-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

    {{-- Watermark --}}
    <div style="margin-top:2.5rem;display:flex;justify-content:flex-start;">
        <img src="{{ asset('bagroung_logo.png') }}" alt="" aria-hidden="true"
             style="width:550px;height:auto;opacity:0.13;pointer-events:none;user-select:none;display:block;">
    </div>

@push('scripts')
<script>
(function() {
    const TZ = '{{ auth()->user()->timezone ?? 'Africa/Algiers' }}';
    const DAYS   = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
    const MONTHS = ['janvier','février','mars','avril','mai','juin',
                    'juillet','août','septembre','octobre','novembre','décembre'];

    function updateDashClock() {
        const now  = new Date(new Date().toLocaleString('en-US', { timeZone: TZ }));
        const day  = DAYS[now.getDay()];
        const date = now.getDate();
        const mon  = MONTHS[now.getMonth()];
        const yr   = now.getFullYear();
        const hh   = String(now.getHours()).padStart(2,'0');
        const mm   = String(now.getMinutes()).padStart(2,'0');
        const ss   = String(now.getSeconds()).padStart(2,'0');

        const dateEl = document.getElementById('dash-date');
        const timeEl = document.getElementById('dash-time');
        if (dateEl) dateEl.textContent = day + ' ' + date + ' ' + mon + ' ' + yr;
        if (timeEl) timeEl.textContent = hh + ':' + mm + ':' + ss;
    }

    updateDashClock();
    setInterval(updateDashClock, 1000);
})();
</script>
@endpush

@endsection

