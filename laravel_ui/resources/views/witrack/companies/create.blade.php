@extends('layouts.witrack')

@push('styles')
<style>
/* ── Page layout ── */
.create-wrap {
    max-width: 680px;
}

/* ── Breadcrumb ── */
.breadcrumb {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .82rem;
    font-weight: 600;
    color: #9bb0b0;
    text-decoration: none;
    margin-bottom: 1.4rem;
    transition: color 140ms;
}
.breadcrumb:hover { color: #0c7070; }

/* ── Hero banner ── */
.create-hero {
    background: linear-gradient(135deg, #071f1f 0%, #0a3535 55%, #0c7070 100%);
    border-radius: 20px;
    padding: 1.8rem 2rem;
    display: flex;
    align-items: center;
    gap: 1.4rem;
    margin-bottom: 1.6rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(12,112,112,.22);
}
.create-hero::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.create-hero::after {
    content: '';
    position: absolute; bottom: -70px; right: 80px;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,.04);
}
.hero-icon-wrap {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(255,255,255,.12);
    border: 1.5px solid rgba(255,255,255,.2);
    display: grid; place-items: center;
    flex-shrink: 0; z-index: 1;
}
.hero-text { z-index: 1; }
.hero-title { font-size: 1.3rem; font-weight: 800; color: #fff; line-height: 1.2; }
.hero-sub   { font-size: .82rem; color: rgba(255,255,255,.6); margin-top: 4px; }

/* ── Form card ── */
.form-card {
    background: #fff;
    border: 1.5px solid #dde8e8;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 16px rgba(12,112,112,.07);
}

/* ── Section header inside card ── */
.section-head {
    display: flex;
    align-items: center;
    gap: .85rem;
    padding: 1.1rem 1.6rem;
    border-bottom: 1.5px solid #edf2f2;
    position: relative;
    overflow: hidden;
}
.section-head::after {
    content: '';
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 2px;
}
.section-head.teal::after  { background: linear-gradient(90deg, #0c7070, #1cc4c4); }
.section-head.indigo::after { background: linear-gradient(90deg, #6366f1, #a5b4fc); }
.section-hicon {
    width: 38px; height: 38px; border-radius: 11px;
    display: grid; place-items: center; flex-shrink: 0;
}
.section-htitle { font-size: .95rem; font-weight: 700; color: #1e2c2c; }
.section-hsub   { font-size: .76rem; color: #6b8080; margin-top: 2px; }

.section-body { padding: 1.4rem 1.6rem; display: flex; flex-direction: column; gap: 1rem; }

.section-divider {
    height: 6px;
    background: linear-gradient(180deg, #f4f7f7, #fff);
    border-top: 1.5px solid #edf2f2;
    border-bottom: 1.5px solid #edf2f2;
}

/* ── Form fields ── */
.fg { display: flex; flex-direction: column; gap: .32rem; }
.fg label {
    font-size: .82rem; font-weight: 700; color: #2c4040;
    letter-spacing: .01em;
    display: flex; align-items: center; gap: .25rem;
}
.req { color: #dc2626; }
.fg input {
    width: 100%;
    border: 1.5px solid #d0e2e2;
    border-radius: 10px;
    padding: .62rem .95rem;
    font: inherit; font-size: .88rem; color: #1e2c2c;
    background: #fafefe;
    outline: none;
    transition: border-color 150ms, box-shadow 150ms;
}
.fg input:focus {
    border-color: #0c7070;
    box-shadow: 0 0 0 3px rgba(12,112,112,.12);
    background: #fff;
}
.fg-hint { font-size: .74rem; color: #9bb0b0; }
.fg-error { font-size: .78rem; color: #dc2626; display: flex; align-items: center; gap: .3rem; }

.fg-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media(max-width:580px) { .fg-row { grid-template-columns: 1fr; } }

/* ── Input with icon prefix ── */
.input-icon-wrap { position: relative; }
.input-icon-wrap .icon-pfx {
    position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
    color: #9bb0b0; display: grid; place-items: center; pointer-events: none;
}
.input-icon-wrap input { padding-left: 2.4rem; }
.input-icon-wrap input:focus ~ .icon-pfx,
.input-icon-wrap input:focus + .icon-pfx { color: #0c7070; }

/* ── Password wrap ── */
.pw-wrap { position: relative; }
.pw-wrap input { padding-right: 2.8rem; }
.pw-toggle {
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: #9bb0b0;
    padding: .2rem; display: grid; place-items: center; transition: color 140ms;
}
.pw-toggle:hover { color: #0c7070; }

/* ── Strength meter ── */
.pw-strength { display: flex; flex-direction: column; gap: .3rem; }
.pw-bars { display: flex; gap: .3rem; }
.pw-bar { flex: 1; height: 4px; border-radius: 99px; background: #e2eaea; transition: background .3s; }
.pw-label { font-size: .72rem; font-weight: 600; color: #9bb0b0; }

/* ── Info band ── */
.info-band {
    background: #e6f7f7; border: 1.5px solid #b8e2e2; border-radius: 10px;
    padding: .7rem 1rem; display: flex; align-items: flex-start; gap: .6rem;
    font-size: .8rem; color: #0a5050; line-height: 1.5;
}
.info-band svg { flex-shrink: 0; margin-top: 1px; }

/* ── Footer actions ── */
.form-footer {
    padding: 1.1rem 1.6rem;
    border-top: 1.5px solid #edf2f2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .8rem;
    background: #fafefe;
}
.btn-create {
    display: inline-flex; align-items: center; gap: .5rem;
    background: linear-gradient(135deg, #0c7070, #14a8a8);
    color: #fff; border: none; border-radius: 11px;
    padding: .68rem 1.5rem; font: inherit; font-size: .9rem; font-weight: 700;
    cursor: pointer; transition: opacity 140ms, transform 120ms;
    box-shadow: 0 4px 14px rgba(12,112,112,.28);
}
.btn-create:hover  { opacity: .9; transform: translateY(-1px); }
.btn-create:active { transform: translateY(0); }
.btn-back {
    display: inline-flex; align-items: center; gap: .5rem;
    background: #fff; color: #4a6868; border: 1.5px solid #d0e2e2;
    border-radius: 11px; padding: .66rem 1.2rem;
    font: inherit; font-size: .88rem; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: background 140ms, border-color 140ms;
}
.btn-back:hover { background: #f4fafa; border-color: #a0cccc; }
</style>
@endpush

@section('content')
<div class="create-wrap">

    {{-- Breadcrumb --}}
    <a href="{{ route('witrack.dashboard') }}" class="breadcrumb">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Retour aux entreprises
    </a>

    {{-- Hero --}}
    <div class="create-hero">
        <div class="hero-icon-wrap">
            <img src="{{ asset('logo_witrack.png') }}" alt="Witrack" style="width:40px;height:40px;object-fit:contain;">
        </div>
        <div class="hero-text">
            <div class="hero-title">Nouvelle entreprise</div>
            <div class="hero-sub">Créez une entreprise et son compte administrateur en une seule étape</div>
        </div>
    </div>

    {{-- Form card --}}
    <div class="form-card">
        <form method="POST" action="{{ route('witrack.companies.store') }}">
            @csrf

            {{-- ── Section 1 : Company ── --}}
            <div class="section-head teal">
                <div class="section-hicon" style="background:#e6f4f4;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0c7070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                        <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                    </svg>
                </div>
                <div>
                    <div class="section-htitle">Entreprise</div>
                    <div class="section-hsub">Identité de la société cliente</div>
                </div>
            </div>

            <div class="section-body">
                <div class="fg">
                    <label for="company_name">
                        Nom de l'entreprise <span class="req">*</span>
                    </label>
                    <div class="input-icon-wrap">
                        <input type="text" id="company_name" name="company_name"
                               value="{{ old('company_name') }}"
                               placeholder="Ex : Acme Corp"
                               required autocomplete="off">
                        <span class="icon-pfx">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                        </span>
                    </div>
                    @error('company_name')
                        <div class="fg-error">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- ── Divider ── --}}
            <div class="section-divider"></div>

            {{-- ── Section 2 : Admin account ── --}}
            <div class="section-head indigo">
                <div class="section-hicon" style="background:#f0f0ff;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                        <path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="section-htitle">Compte administrateur</div>
                    <div class="section-hsub">Accès principal de cette entreprise</div>
                </div>
            </div>

            <div class="section-body">

                <div class="info-band">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    L'administrateur peut ensuite gérer les utilisateurs, les documents et la configuration de son espace depuis son tableau de bord.
                </div>

                <div class="fg">
                    <label for="admin_name">Nom d'utilisateur <span class="req">*</span></label>
                    <div class="input-icon-wrap">
                        <input type="text" id="admin_name" name="admin_name"
                               value="{{ old('admin_name') }}"
                               placeholder="Ex : admin_acme"
                               required autocomplete="off">
                        <span class="icon-pfx">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                    </div>
                    <div class="fg-hint">Doit être unique sur toute la plateforme.</div>
                    @error('admin_name')
                        <div class="fg-error">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="fg-row">
                    <div class="fg">
                        <label for="admin_password">Mot de passe <span class="req">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" id="admin_password" name="admin_password"
                                   placeholder="Min. 8 caractères"
                                   required oninput="checkStrength(this.value)"
                                   autocomplete="new-password">
                            <button type="button" class="pw-toggle" onclick="togglePw('admin_password', this)">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('admin_password')
                            <div class="fg-error">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="fg">
                        <label for="admin_password_confirmation">Confirmer <span class="req">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" id="admin_password_confirmation" name="admin_password_confirmation"
                                   placeholder="Répétez le mot de passe"
                                   required autocomplete="new-password">
                            <button type="button" class="pw-toggle" onclick="togglePw('admin_password_confirmation', this)">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Password strength meter --}}
                <div class="pw-strength" id="pw-strength" style="display:none;">
                    <div class="pw-bars">
                        <div class="pw-bar" id="pw-b1"></div>
                        <div class="pw-bar" id="pw-b2"></div>
                        <div class="pw-bar" id="pw-b3"></div>
                        <div class="pw-bar" id="pw-b4"></div>
                    </div>
                    <div class="pw-label" id="pw-label">Force du mot de passe</div>
                </div>

            </div>

            {{-- ── Footer ── --}}
            <div class="form-footer">
                <a href="{{ route('witrack.dashboard') }}" class="btn-back">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Annuler
                </a>
                <button type="submit" class="btn-create">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Créer l'entreprise
                </button>
            </div>

        </form>
    </div>

</div>

@push('scripts')
<script>
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.style.color = inp.type === 'text' ? '#0c7070' : '#9bb0b0';
}

function checkStrength(val) {
    const el = document.getElementById('pw-strength');
    if (!val) { el.style.display = 'none'; return; }
    el.style.display = 'flex';
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
    const labels = ['Très faible', 'Faible', 'Moyen', 'Fort'];
    for (let i = 1; i <= 4; i++) {
        document.getElementById('pw-b' + i).style.background = i <= score ? colors[score - 1] : '#e2eaea';
    }
    document.getElementById('pw-label').textContent = labels[score - 1] || 'Très faible';
    document.getElementById('pw-label').style.color  = colors[score - 1] || colors[0];
}
</script>
@endpush

@endsection
