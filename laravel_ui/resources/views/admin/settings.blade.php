@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
/* ── Crop Modal ── */
.crop-modal-overlay {
    display:none; position:fixed; inset:0; z-index:9000;
    background:rgba(0,0,0,.65); backdrop-filter:blur(4px);
    align-items:center; justify-content:center;
}
.crop-modal-overlay.open { display:flex; }
.crop-modal {
    background:#fff; border-radius:20px;
    width:min(96vw, 700px);
    box-shadow:0 24px 64px rgba(0,0,0,.35);
    overflow:hidden;
    display:flex; flex-direction:column;
    max-height:92vh;
}
.crop-modal-header {
    padding:1.1rem 1.5rem;
    border-bottom:1.5px solid #edf2f2;
    display:flex; align-items:center; justify-content:space-between;
}
.crop-modal-title { font-size:1rem; font-weight:800; color:#1e2c2c; }
.crop-modal-close {
    width:32px; height:32px; border-radius:50%;
    background:#f0f4f4; border:none; cursor:pointer;
    display:grid; place-items:center; color:#4a7070;
    transition:background 140ms;
}
.crop-modal-close:hover { background:#e0ecec; }
.crop-area {
    background:#1a1a1a;
    flex:1; overflow:hidden;
    max-height:55vh;
    display:flex; align-items:center; justify-content:center;
}
.crop-area img { max-width:100%; display:block; }
.crop-controls {
    padding:1rem 1.5rem;
    border-top:1.5px solid #edf2f2;
    display:flex; flex-direction:column; gap:.9rem;
}
.crop-ctrl-row {
    display:flex; align-items:center; gap:1rem;
}
.crop-ctrl-label {
    font-size:.78rem; font-weight:700; color:#4a7070;
    min-width:72px;
}
.crop-icon-btn {
    width:36px; height:36px; border-radius:10px;
    background:#f0f7f7; border:1.5px solid #d0e2e2;
    cursor:pointer; display:grid; place-items:center;
    color:var(--teal); transition:background 130ms;
}
.crop-icon-btn:hover { background:#d5f0f0; }
.crop-zoom-slider {
    flex:1; accent-color:var(--teal);
    height:4px; cursor:pointer;
}
.crop-actions {
    display:flex; align-items:center; justify-content:flex-end;
    gap:.7rem; padding:1rem 1.5rem;
    border-top:1.5px solid #edf2f2;
}
.crop-btn-cancel {
    padding:.5rem 1.1rem; border-radius:10px;
    background:#f0f4f4; border:1.5px solid #d0e2e2;
    font-size:.85rem; font-weight:600; color:#4a7070;
    cursor:pointer;
}
.crop-btn-cancel:hover { background:#e4ecec; }
.crop-btn-save {
    padding:.5rem 1.4rem; border-radius:10px;
    background:linear-gradient(135deg,var(--teal),var(--teal-mid));
    border:none; font-size:.85rem; font-weight:700; color:#fff;
    cursor:pointer; box-shadow:0 3px 10px var(--teal-shadow);
    transition:opacity 140ms;
}
.crop-btn-save:hover { opacity:.88; }
/* ═══════════════════════════════════════════════
   SETTINGS PAGE — VISUAL REDESIGN
═══════════════════════════════════════════════ */

/* ── Hero banner ── */
.settings-hero {
    background: linear-gradient(135deg, var(--teal) 0%, var(--teal-mid) 55%, var(--teal-light) 100%);
    border-radius: 20px;
    padding: 2rem 2.2rem;
    display: flex;
    align-items: center;
    gap: 1.8rem;
    margin-bottom: 1.6rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px var(--teal-shadow);
}
.settings-hero::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:240px; height:240px; border-radius:50%;
    background:rgba(255,255,255,.07);
}
.settings-hero::after {
    content:''; position:absolute; bottom:-70px; right:90px;
    width:180px; height:180px; border-radius:50%;
    background:rgba(255,255,255,.05);
}
.hero-avatar-wrap {
    position: relative;
    flex-shrink: 0;
    cursor: pointer;
}
.hero-avatar {
    width: 88px; height: 88px;
    border-radius: 50%;
    background: rgba(255,255,255,.18);
    border: 3px solid rgba(255,255,255,.45);
    display: grid; place-items: center;
    overflow: hidden;
    font-size: 2rem; font-weight: 800; color: #fff;
    transition: filter 200ms;
}
.hero-avatar img { width:100%; height:100%; object-fit:cover; }
.hero-avatar-overlay {
    position:absolute; inset:0; border-radius:50%;
    background:rgba(0,0,0,.35);
    display:grid; place-items:center;
    opacity:0; transition:opacity 180ms;
}
.hero-avatar-wrap:hover .hero-avatar-overlay { opacity:1; }
.hero-info { flex:1; z-index:1; }
.hero-name  { font-size:1.5rem; font-weight:800; color:#fff; line-height:1.2; }
.hero-role  { font-size:0.82rem; color:rgba(255,255,255,.7); margin-top:4px; }
.hero-badges { display:flex; gap:.5rem; margin-top:.7rem; flex-wrap:wrap; }
.hero-badge {
    display:inline-flex; align-items:center; gap:5px;
    background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.25);
    border-radius:20px; padding:.25rem .8rem;
    font-size:.75rem; font-weight:600; color:rgba(255,255,255,.9);
    backdrop-filter:blur(4px);
}
.hero-edit-btn {
    z-index:1; background:rgba(255,255,255,.15); border:1.5px solid rgba(255,255,255,.3);
    border-radius:10px; padding:.55rem 1.1rem;
    font:.inherit; font-size:.84rem; font-weight:600; color:#fff;
    cursor:pointer; display:flex; align-items:center; gap:.5rem;
    transition:background 160ms;
    text-decoration:none;
}
.hero-edit-btn:hover { background:rgba(255,255,255,.25); }

/* ── Layout ── */
.settings-layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 1.4rem;
    align-items: start;
}

/* ── Sidebar nav ── */
.settings-nav {
    background: #fff;
    border: 1.5px solid #dde8e8;
    border-radius: 18px;
    padding: .5rem;
    display: flex;
    flex-direction: column;
    gap: .15rem;
    position: sticky;
    top: 1.5rem;
    overflow: hidden;
}
.snav-label {
    font-size:.68rem; font-weight:700; letter-spacing:.08em;
    color:#9bb0b0; text-transform:uppercase;
    padding:.65rem .9rem .3rem;
}
.snav-item {
    display:flex; align-items:center; gap:.85rem;
    padding:.72rem 1rem; border-radius:12px;
    background:none; border:none; cursor:pointer;
    font:inherit; text-align:left; width:100%;
    transition:background 130ms, color 130ms;
    position:relative;
    text-decoration:none;
}
.snav-icon {
    width:36px; height:36px; border-radius:10px;
    display:grid; place-items:center;
    flex-shrink:0; transition:transform 150ms;
}
.snav-item:hover .snav-icon { transform:scale(1.08); }
.snav-text { flex:1; }
.snav-title { font-size:.87rem; font-weight:600; color:#4a6666; display:block; transition:color 130ms; }
.snav-desc  { font-size:.72rem; color:#9bb0b0; display:block; margin-top:1px; transition:color 130ms; }
.snav-item:hover .snav-title { color:#0c7070; }
.snav-item:hover { background:#f5fbfb; }
.snav-item.active { background:linear-gradient(135deg,#e6f4f4,#f0fafa); }
.snav-item.active .snav-title { color:#0c7070; font-weight:700; }
.snav-item.active::before {
    content:''; position:absolute; left:0; top:20%; bottom:20%;
    width:3px; border-radius:0 3px 3px 0;
    background:#0c7070;
}
.snav-divider { height:1px; background:#edf2f2; margin:.3rem .9rem; }

/* ── Panels ── */
.settings-panel { display:none; flex-direction:column; gap:1.3rem; }
.settings-panel.active { display:flex; }

/* ── Cards ── */
.scard {
    background:#fff;
    border:1.5px solid #dde8e8;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 2px 12px rgba(12,112,112,.06);
    transition: box-shadow 200ms;
}
.scard:hover { box-shadow:0 4px 20px rgba(12,112,112,.10); }
.scard-head {
    padding:1.2rem 1.6rem;
    border-bottom:1px solid #edf2f2;
    display:flex; align-items:center; gap:.9rem;
    position:relative; overflow:hidden;
}
.scard-head::after {
    content:''; position:absolute; bottom:0; left:0; right:0;
    height:2px;
}
.scard-head.teal::after  { background:linear-gradient(90deg,var(--teal),var(--teal-light)); }
.scard-head.gold::after  { background:linear-gradient(90deg,#d4a017,#f5c842); }
.scard-head.indigo::after{ background:linear-gradient(90deg,#6366f1,#a5b4fc); }
.scard-head.green::after { background:linear-gradient(90deg,#16a34a,#4ade80); }
.scard-hicon {
    width:42px; height:42px; border-radius:12px;
    display:grid; place-items:center; flex-shrink:0;
}
.scard-htitle { font-weight:700; font-size:1rem; color:#1e2c2c; }
.scard-hsub   { font-size:.78rem; color:#6b8080; margin-top:2px; }
.scard-body   { padding:1.5rem 1.6rem; display:flex; flex-direction:column; gap:1.15rem; }

/* ── Avatar section ── */
.avatar-section {
    display:flex; align-items:center; gap:1.4rem;
    padding:1.3rem 1.5rem;
    background:linear-gradient(135deg,#f4fbfb,#e8f7f7);
    border:1.5px dashed #a0cccc;
    border-radius:14px;
    cursor:pointer;
    transition:border-color 150ms, background 150ms;
    position:relative;
}
.avatar-section:hover { border-color:var(--teal); background:linear-gradient(135deg,#e8f7f7,#d5f0f0); }
.av-ring {
    width:80px; height:80px; border-radius:50%;
    display:grid; place-items:center;
    flex-shrink:0; overflow:hidden;
    background:linear-gradient(135deg,var(--teal),var(--teal-mid));
    border:3px solid #fff;
    box-shadow:0 4px 14px var(--teal-shadow);
    font-size:1.6rem; font-weight:800; color:#fff;
    position:relative;
}
.av-ring img { width:100%; height:100%; object-fit:cover; }
.av-ring-overlay {
    position:absolute; inset:0; border-radius:50%;
    background:rgba(0,0,0,.38);
    display:grid; place-items:center;
    opacity:0; transition:opacity 160ms;
}
.avatar-section:hover .av-ring-overlay { opacity:1; }
.av-info-title { font-size:.9rem; font-weight:700; color:#1e2c2c; }
.av-info-sub   { font-size:.76rem; color:#6b8080; margin-top:3px; }
.av-btn {
    margin-left:auto;
    background:#fff; border:1.5px solid #c5dede; border-radius:10px;
    padding:.5rem 1rem; font:.inherit; font-size:.82rem; font-weight:600;
    color:var(--teal); cursor:pointer; display:flex; align-items:center; gap:.4rem;
    transition:background 140ms, border-color 140ms;
    white-space:nowrap;
}
.av-btn:hover { background:var(--teal-soft); border-color:var(--teal); }

/* ── Form elements ── */
.form-row  { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.form-group { display:flex; flex-direction:column; gap:.32rem; }
.form-group label { font-size:.82rem; font-weight:600; color:#2c4040; letter-spacing:.01em; }
.form-group input,
.form-group select {
    width:100%;
    border:1.5px solid #d0e2e2;
    border-radius:10px;
    padding:.6rem .9rem;
    font:.inherit; font-size:.88rem; color:#1e2c2c;
    background:#fafefe;
    transition:border-color 150ms, box-shadow 150ms;
    outline:none;
}
.form-group input:focus,
.form-group select:focus {
    border-color:var(--teal);
    box-shadow:0 0 0 3px var(--teal-shadow);
    background:#fff;
}
.form-hint { font-size:.74rem; color:#6b8080; margin-top:2px; }

/* ── Password field with toggle ── */
.pw-wrap { position:relative; }
.pw-wrap input { padding-right:2.8rem; }
.pw-toggle {
    position:absolute; right:.7rem; top:50%; transform:translateY(-50%);
    background:none; border:none; cursor:pointer; color:#7a9a9a;
    padding:.2rem; display:grid; place-items:center;
    transition:color 140ms;
}
.pw-toggle:hover { color:#0c7070; }

/* ── Password strength ── */
.pw-strength { display:flex; flex-direction:column; gap:.35rem; }
.pw-strength-bars { display:flex; gap:.3rem; }
.pw-bar {
    flex:1; height:4px; border-radius:99px;
    background:#e2eaea; transition:background .3s;
}
.pw-strength-label { font-size:.74rem; font-weight:600; color:#9bb0b0; }

/* ── Color swatches ── */
.color-grid { display:flex; gap:.7rem; flex-wrap:wrap; align-items:center; }
.cswatch {
    width:40px; height:40px; border-radius:10px;
    border:2.5px solid transparent;
    cursor:pointer; position:relative;
    transition:transform 140ms, border-color 140ms, box-shadow 140ms;
    display:grid; place-items:center;
}
.cswatch:hover   { transform:scale(1.12); box-shadow:0 4px 12px rgba(0,0,0,.2); }
.cswatch.chosen  { border-color:#fff; box-shadow:0 0 0 2.5px #1e2c2c, 0 4px 12px rgba(0,0,0,.2); transform:scale(1.1); }
.cswatch .ck { display:none; }
.cswatch.chosen .ck { display:block; }
.color-custom-wrap {
    width:40px; height:40px; border-radius:10px;
    border:1.5px dashed #b0cccc; cursor:pointer;
    display:grid; place-items:center;
    overflow:hidden;
    transition:border-color 140ms;
}
.color-custom-wrap:hover { border-color:var(--teal); }
.color-custom-wrap input[type=color] {
    width:56px; height:56px; border:none; padding:0;
    cursor:pointer; transform:scale(1.2);
    background:transparent;
}

/* ── Theme color preview strip ── */
.theme-preview {
    border-radius:12px; overflow:hidden;
    border:1.5px solid #dde8e8;
    display:flex; align-items:stretch;
}
.theme-sidebar-mock {
    width:38px; display:flex; flex-direction:column;
    align-items:center; padding:.5rem .3rem; gap:.4rem;
    transition:background .3s;
}
.theme-sidebar-dot {
    width:20px; height:4px; border-radius:99px;
    background:rgba(255,255,255,.35);
}
.theme-sidebar-dot.active { background:rgba(255,255,255,.8); width:20px; }
.theme-main-mock {
    flex:1; background:#f4f7f7; padding:.7rem;
    display:flex; flex-direction:column; gap:.4rem;
}
.theme-mock-bar {
    background:#fff; border-radius:6px; height:12px;
    box-shadow:0 1px 3px rgba(0,0,0,.06);
}
.theme-mock-bar.accent { height:8px; border-radius:4px; width:60%; transition:background .3s; }

/* ── Logo upload ── */
.logo-drop {
    background:linear-gradient(135deg,#f7fafa,#eef5f5);
    border:2px dashed #b0cccc;
    border-radius:14px;
    padding:2rem;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:.8rem; min-height:120px; cursor:pointer;
    transition:border-color 160ms, background 160ms;
    text-align:center;
}
.logo-drop:hover { border-color:var(--teal); background:linear-gradient(135deg,#e8f7f7,#d8f0f0); }
.logo-drop-icon { color:#7ab0b0; }
.logo-drop-label { font-size:.85rem; font-weight:600; color:#2c4040; }
.logo-drop-sub   { font-size:.74rem; color:#8aacac; }
.logo-drop img   { max-height:64px; max-width:100%; object-fit:contain; border-radius:6px; }

/* ── Clock panel ── */
.clock-display {
    background:linear-gradient(135deg,var(--teal),var(--teal-mid));
    border-radius:18px; padding:2rem 2.5rem;
    display:flex; align-items:center; justify-content:space-between;
    gap:1.5rem; box-shadow:0 6px 24px var(--teal-shadow);
    position:relative; overflow:hidden;
}
.clock-display::before {
    content:''; position:absolute; top:-30px; right:-30px;
    width:160px; height:160px; border-radius:50%;
    background:rgba(255,255,255,.07);
}
.clock-digits {
    font-size:3rem; font-weight:900; color:#fff;
    letter-spacing:-.02em; line-height:1; font-variant-numeric:tabular-nums;
    z-index:1;
}
.clock-seconds {
    font-size:1.5rem; font-weight:700; color:rgba(255,255,255,.65); vertical-align:super; margin-left:3px;
}
.clock-date-block { text-align:right; z-index:1; }
.clock-dow  { font-size:.9rem; font-weight:700; color:rgba(255,255,255,.8); text-transform:capitalize; }
.clock-full { font-size:.78rem; color:rgba(255,255,255,.6); margin-top:3px; }

/* ── Actions ── */
.form-actions { display:flex; justify-content:flex-end; gap:.65rem; padding-top:.3rem; }
.btn-save {
    display:inline-flex; align-items:center; gap:.5rem;
    background:linear-gradient(135deg,var(--teal),var(--teal-mid));
    color:#fff; border:none; border-radius:10px;
    padding:.65rem 1.4rem; font:.inherit; font-size:.88rem; font-weight:700;
    cursor:pointer; transition:opacity 150ms, transform 120ms;
    box-shadow:0 3px 10px var(--teal-shadow);
}
.btn-save:hover { opacity:.9; transform:translateY(-1px); }
.btn-save:active { transform:translateY(0); }
.btn-cancel {
    display:inline-flex; align-items:center; gap:.5rem;
    background:#fff; color:#4a6666; border:1.5px solid #d0e2e2;
    border-radius:10px; padding:.63rem 1.2rem;
    font:.inherit; font-size:.88rem; font-weight:600;
    cursor:pointer; transition:background 140ms, border-color 140ms;
}
.btn-cancel:hover { background:#f4fbfb; border-color:#a0cccc; }

/* ── Info band ── */
.info-band {
    background:var(--teal-soft); border:1.5px solid #c8e8e8;
    border-radius:10px; padding:.7rem 1rem;
    display:flex; align-items:center; gap:.6rem;
    font-size:.8rem; color:var(--teal);
}

@media (max-width:860px) {
    .settings-layout { grid-template-columns:1fr; }
    .settings-nav { flex-direction:row; flex-wrap:wrap; position:static; gap:.15rem; }
    .snav-desc { display:none; }
    .snav-item.active::before { display:none; }
    .form-row { grid-template-columns:1fr; }
    .clock-digits { font-size:2.2rem; }
}
</style>
@endpush

@section('content')

{{-- ── Hero Banner ──────────────────────────────── --}}
<div class="settings-hero">
    <label class="hero-avatar-wrap" for="hero-avatar-input" title="Changer la photo">
        <div class="hero-avatar" id="hero-avatar">
            @if(auth()->user()->avatar)
                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar">
            @else
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            @endif
        </div>
        <div class="hero-avatar-overlay">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
        <input type="file" id="hero-avatar-input" accept="image/*" style="display:none;" onchange="syncHeroAvatar(this)">
    </label>

    <div class="hero-info">
        <div class="hero-name">{{ auth()->user()->name }}</div>
        <div class="hero-role">Administrateur du système</div>
        <div class="hero-badges">
            <span class="hero-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Admin
            </span>
            <span class="hero-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ auth()->user()->timezone ?? 'Africa/Algiers' }}
            </span>
        </div>
    </div>

    <button type="button" class="hero-edit-btn" onclick="switchTab('profile', document.querySelector('[data-tab=profile]'))">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Modifier le profil
    </button>
</div>

{{-- ── Grid Layout ─────────────────────────────── --}}
<div class="settings-layout">

    {{-- ── Left Nav ── --}}
    <nav class="settings-nav">
        <div class="snav-label">Mon compte</div>

        <button class="snav-item active" data-tab="profile" onclick="switchTab('profile',this)" type="button">
            <div class="snav-icon" style="background:#e6f4f4;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0c7070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="snav-text">
                <span class="snav-title">Profil</span>
                <span class="snav-desc">Nom & photo</span>
            </div>
        </button>

        <button class="snav-item" data-tab="password" onclick="switchTab('password',this)" type="button">
            <div class="snav-icon" style="background:#fef9ec;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d4a017" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
            <div class="snav-text">
                <span class="snav-title">Mot de passe</span>
                <span class="snav-desc">Sécurité du compte</span>
            </div>
        </button>

        <div class="snav-divider"></div>
        <div class="snav-label">Personnalisation</div>

        <button class="snav-item" data-tab="company" onclick="switchTab('company',this)" type="button">
            <div class="snav-icon" style="background:#f0f0ff;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            </div>
            <div class="snav-text">
                <span class="snav-title">Entreprise & Thème</span>
                <span class="snav-desc">Logo & couleurs</span>
            </div>
        </button>

        <button class="snav-item" data-tab="datetime" onclick="switchTab('datetime',this)" type="button">
            <div class="snav-icon" style="background:#ecfdf5;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="snav-text">
                <span class="snav-title">Date & Heure</span>
                <span class="snav-desc">Fuseau horaire</span>
            </div>
        </button>
    </nav>

    {{-- ── Panels ── --}}
    <div>

        {{-- ═══════ PROFILE ═══════ --}}
        <div class="settings-panel active" id="panel-profile">
            <div class="scard">
                <div class="scard-head teal">
                    <div class="scard-hicon" style="background:#e6f4f4;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0c7070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <div class="scard-htitle">Informations du profil</div>
                        <div class="scard-hsub">Photo de profil et nom affiché</div>
                    </div>
                </div>
                <div class="scard-body">
                    <form method="POST" action="{{ route('admin.settings.profile') }}" enctype="multipart/form-data" id="profile-form">
                        @csrf

                        {{-- Avatar upload --}}
                        <label class="avatar-section" for="avatar-input">
                            <div class="av-ring" id="av-ring">
                                @if(auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                @endif
                                <div class="av-ring-overlay">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                </div>
                            </div>
                            <div>
                                <div class="av-info-title">Changer la photo de profil</div>
                                <div class="av-info-sub">JPG, PNG ou GIF · max 2 Mo</div>
                            </div>
                            <div class="av-btn">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.29"/></svg>
                                Parcourir
                            </div>
                            <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none;" onchange="previewAvatar(this)">
                        </label>

                        <div class="form-group">
                            <label for="name">Nom d'affichage</label>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   placeholder="Votre nom complet" required>
                            @error('name')
                                <div class="form-hint" style="color:#dc2626;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="info-band">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Le nom doit être unique. Il apparaît dans la barre latérale.
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="document.getElementById('name').value='{{ auth()->user()->name }}'">Annuler</button>
                            <button type="submit" class="btn-save">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════ PASSWORD ═══════ --}}
        <div class="settings-panel" id="panel-password">
            <div class="scard">
                <div class="scard-head gold">
                    <div class="scard-hicon" style="background:#fef9ec;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d4a017" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </div>
                    <div>
                        <div class="scard-htitle">Changer le mot de passe</div>
                        <div class="scard-hsub">Choisissez un mot de passe fort (min. 8 caractères)</div>
                    </div>
                </div>
                <div class="scard-body">
                    <form method="POST" action="{{ route('admin.settings.password') }}">
                        @csrf

                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel</label>
                            <div class="pw-wrap">
                                <input type="password" id="current_password" name="current_password" placeholder="••••••••" required>
                                <button class="pw-toggle" type="button" onclick="togglePw('current_password',this)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="form-hint" style="color:#dc2626;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Nouveau mot de passe</label>
                                <div class="pw-wrap">
                                    <input type="password" id="password" name="password" placeholder="••••••••" required
                                           oninput="checkStrength(this.value)">
                                    <button class="pw-toggle" type="button" onclick="togglePw('password',this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirmer le mot de passe</label>
                                <div class="pw-wrap">
                                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                                    <button class="pw-toggle" type="button" onclick="togglePw('password_confirmation',this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="form-hint" style="color:#dc2626;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Strength meter --}}
                        <div class="pw-strength" id="pw-strength" style="display:none;">
                            <div class="pw-strength-bars">
                                <div class="pw-bar" id="pw-b1"></div>
                                <div class="pw-bar" id="pw-b2"></div>
                                <div class="pw-bar" id="pw-b3"></div>
                                <div class="pw-bar" id="pw-b4"></div>
                            </div>
                            <div class="pw-strength-label" id="pw-strength-label">Force du mot de passe</div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════ COMPANY & THEME ═══════ --}}
        <div class="settings-panel" id="panel-company">
            <div class="scard">
                <div class="scard-head indigo">
                    <div class="scard-hicon" style="background:#f0f0ff;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                    </div>
                    <div>
                        <div class="scard-htitle">Identité de l'entreprise</div>
                        <div class="scard-hsub">Nom, logo et couleur principale de l'interface</div>
                    </div>
                </div>
                <div class="scard-body">
                    <form method="POST" action="{{ route('admin.settings.company') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="company_name">Nom de l'entreprise</label>
                            <input type="text" id="company_name" name="company_name"
                                   value="{{ old('company_name', auth()->user()->company_name) }}"
                                   placeholder="Ex: Acme Corp">
                        </div>

                        {{-- Logo upload --}}
                        <div class="form-group">
                            <label>Logo de l'entreprise</label>
                            {{-- visual drop zone (clicking it triggers the hidden input below) --}}
                            <div class="logo-drop" id="logo-drop-area" onclick="document.getElementById('company_logo').click()" style="cursor:pointer;">
                                <div id="logo-drop-content">
                                    @if(auth()->user()->company_logo)
                                        <img src="{{ Storage::url(auth()->user()->company_logo) }}" style="max-height:70px;max-width:100%;object-fit:contain;border-radius:6px;" alt="logo">
                                    @else
                                        <div class="logo-drop-icon">
                                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        </div>
                                        <div class="logo-drop-label">Glissez un fichier ici ou cliquez pour parcourir</div>
                                        <div class="logo-drop-sub">PNG, SVG, JPG · max 4 Mo</div>
                                    @endif
                                </div>
                            </div>
                            {{-- file input lives OUTSIDE the drop zone so it is never destroyed by innerHTML --}}
                            <input type="file" id="company_logo" name="company_logo" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                        </div>

                        {{-- Theme color --}}
                        <div class="form-group">
                            <label>Couleur principale</label>
                            <div class="color-grid" id="color-grid">
                                @php $currentColor = old('theme_color', auth()->user()->theme_color ?? '#0c7070'); @endphp
                                @foreach(['#0c7070','#6366f1','#e11d48','#d4a017','#0891b2','#16a34a','#7c3aed','#ea580c'] as $c)
                                    <div class="cswatch {{ $currentColor === $c ? 'chosen' : '' }}"
                                         style="background:{{ $c }};"
                                         title="{{ $c }}"
                                         onclick="pickColor('{{ $c }}',this)">
                                        <svg class="ck" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                @endforeach
                                <div class="color-custom-wrap" title="Couleur personnalisée">
                                    <input type="color" id="custom-color"
                                           value="{{ $currentColor }}"
                                           onchange="pickColor(this.value,null,this)">
                                </div>
                            </div>
                            <input type="hidden" name="theme_color" id="theme_color_input" value="{{ $currentColor }}">
                        </div>

                        {{-- Preview strip --}}
                        <div class="form-group">
                            <label>Aperçu du thème</label>
                            <div class="theme-preview">
                                <div class="theme-sidebar-mock" id="theme-sidebar-mock" style="background:{{ $currentColor }};">
                                    <div class="theme-sidebar-dot active"></div>
                                    <div class="theme-sidebar-dot"></div>
                                    <div class="theme-sidebar-dot"></div>
                                    <div class="theme-sidebar-dot"></div>
                                </div>
                                <div class="theme-main-mock">
                                    <div class="theme-mock-bar" style="width:80%;height:10px;"></div>
                                    <div class="theme-mock-bar accent" id="theme-accent-bar" style="background:{{ $currentColor }};opacity:.7;"></div>
                                    <div class="theme-mock-bar" style="width:90%;height:8px;"></div>
                                    <div class="theme-mock-bar" style="width:65%;height:8px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════ DATE & TIME ═══════ --}}
        <div class="settings-panel" id="panel-datetime">
            <div class="scard">
                <div class="scard-head green">
                    <div class="scard-hicon" style="background:#ecfdf5;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div class="scard-htitle">Date &amp; Fuseau horaire</div>
                        <div class="scard-hsub">Heure locale affichée dans le tableau de bord</div>
                    </div>
                </div>
                <div class="scard-body">

                    {{-- Big live clock --}}
                    <div class="clock-display">
                        <div>
                            <div class="clock-digits">
                                <span id="live-hm"></span><span class="clock-seconds" id="live-sec"></span>
                            </div>
                        </div>
                        <div class="clock-date-block">
                            <div class="clock-dow" id="live-dow"></div>
                            <div class="clock-full" id="live-date"></div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.datetime') }}">
                        @csrf
                        <div class="form-group">
                            <label for="timezone">Fuseau horaire</label>
                            <select name="timezone" id="timezone">
                                @foreach($timezones as $tz)
                                    <option value="{{ $tz }}" {{ (auth()->user()->timezone ?? 'Africa/Algiers') === $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- end right column --}}
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
/* ── Tab switching ── */
function switchTab(name, btn) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.snav-item').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + name).classList.add('active');
    // find by data-tab if btn doesn't have it
    const target = (btn && btn.dataset && btn.dataset.tab) ? btn : document.querySelector('[data-tab=' + name + ']');
    if (target) target.classList.add('active');
}

/* ════════════════════════════════════════
   AVATAR CROP MODAL
════════════════════════════════════════ */
let cropperInst  = null;
let cropCallback = null; // function(dataURL, blob) called after crop confirm

function openCropModal(file, callback) {
    cropCallback = callback;
    const reader = new FileReader();
    reader.onload = e => {
        const img   = document.getElementById('crop-img');
        const overlay = document.getElementById('crop-modal-overlay');
        img.src = e.target.result;
        overlay.classList.add('open');
        document.getElementById('crop-zoom-range').value   = 50;
        document.getElementById('crop-rotate-range').value = 0;
        // destroy previous instance
        if (cropperInst) { cropperInst.destroy(); cropperInst = null; }
        cropperInst = new Cropper(img, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.85,
            restore: false,
            guides: false,
            center: true,
            highlight: false,
            cropBoxMovable: false,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
        });
    };
    reader.readAsDataURL(file);
}

function closeCropModal() {
    document.getElementById('crop-modal-overlay').classList.remove('open');
    if (cropperInst) { cropperInst.destroy(); cropperInst = null; }
    cropCallback = null;
}

function onZoomSlider(val) {
    if (!cropperInst) return;
    // val 0-100 → zoom ratio relative to canvas
    const ratio = (val - 50) / 50 * 1.5; // -1.5 to +1.5
    cropperInst.zoomTo(Math.max(0.1, 1 + ratio));
}

function applyCrop() {
    if (!cropperInst || !cropCallback) return;
    const canvas = cropperInst.getCroppedCanvas({ width: 400, height: 400 });
    canvas.toBlob(blob => {
        const dataURL = canvas.toDataURL('image/jpeg', 0.92);
        cropCallback(dataURL, blob);
        closeCropModal();
    }, 'image/jpeg', 0.92);
}

/* ── Hero avatar sync ── */
function syncHeroAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    input.value = ''; // reset so onchange fires again if same file re-selected
    openCropModal(file, (dataURL, blob) => {
        // update UI
        const heroHtml = '<img src="' + dataURL + '" alt="avatar" style="width:100%;height:100%;object-fit:cover;">';
        const overlayHtml = '<div class="av-ring-overlay"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg></div>';
        document.getElementById('hero-avatar').innerHTML = heroHtml;
        const avRing = document.getElementById('av-ring');
        if (avRing) avRing.innerHTML = heroHtml + overlayHtml;
        // inject into profile form file input
        const croppedFile = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(croppedFile);
        document.getElementById('avatar-input').files = dt.files;
    });
}

/* ── Avatar preview (panel) ── */
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    input.value = '';
    openCropModal(file, (dataURL, blob) => {
        const heroHtml = '<img src="' + dataURL + '" alt="avatar" style="width:100%;height:100%;object-fit:cover;">';
        const overlayHtml = '<div class="av-ring-overlay"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg></div>';
        document.getElementById('av-ring').innerHTML = heroHtml + overlayHtml;
        document.getElementById('hero-avatar').innerHTML = heroHtml;
        // put cropped blob back into the file input so the form uploads it
        const croppedFile = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(croppedFile);
        input.files = dt.files;
    });
}

/* ── Logo preview ── */
function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        // Only update the VISUAL inner div — never touch the file input itself
        document.getElementById('logo-drop-content').innerHTML =
            '<img src="' + e.target.result + '" style="max-height:70px;max-width:100%;object-fit:contain;border-radius:6px;" alt="logo">';
    };
    reader.readAsDataURL(input.files[0]);
}

/* ── Color picker ── */
function pickColor(hex, swatchEl, colorInput) {
    document.getElementById('theme_color_input').value = hex;
    document.querySelectorAll('.cswatch').forEach(s => s.classList.remove('chosen'));
    if (swatchEl) swatchEl.classList.add('chosen');
    if (!colorInput) document.getElementById('custom-color').value = hex;
    // update preview
    const mock = document.getElementById('theme-sidebar-mock');
    const accent = document.getElementById('theme-accent-bar');
    if (mock)   mock.style.background = hex;
    if (accent) accent.style.background = hex;
}

/* ── Password show/hide ── */
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.style.color = inp.type === 'text' ? '#0c7070' : '#7a9a9a';
}

/* ── Password strength ── */
function checkStrength(val) {
    const el = document.getElementById('pw-strength');
    if (!val) { el.style.display = 'none'; return; }
    el.style.display = 'flex';
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors  = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels  = ['Très faible','Faible','Moyen','Fort'];
    for (let i = 1; i <= 4; i++) {
        const b = document.getElementById('pw-b' + i);
        b.style.background = i <= score ? colors[score - 1] : '#e2eaea';
    }
    document.getElementById('pw-strength-label').textContent = labels[score - 1] || 'Très faible';
    document.getElementById('pw-strength-label').style.color = colors[score - 1] || colors[0];
}

/* ── Live clock (respects the user's saved timezone) ── */
const SETTINGS_TZ = '{{ auth()->user()->timezone ?? 'Africa/Algiers' }}';
function updateClock() {
    const now  = new Date(new Date().toLocaleString('en-US', { timeZone: SETTINGS_TZ }));
    const hm   = now.toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
    const sec  = String(now.getSeconds()).padStart(2,'0');
    const dow  = now.toLocaleDateString('fr-FR', {weekday:'long'});
    const full = now.toLocaleDateString('fr-FR', {day:'numeric', month:'long', year:'numeric'});
    document.getElementById('live-hm').textContent = hm;
    document.getElementById('live-sec').textContent = sec;
    document.getElementById('live-dow').textContent = dow.charAt(0).toUpperCase() + dow.slice(1);
    document.getElementById('live-date').textContent = full;
}
updateClock();
setInterval(updateClock, 1000);
</script>
@endpush

{{-- ── Avatar Crop Modal ── --}}
<div class="crop-modal-overlay" id="crop-modal-overlay">
    <div class="crop-modal">
        <div class="crop-modal-header">
            <span class="crop-modal-title">Modifier la photo</span>
            <button class="crop-modal-close" type="button" onclick="closeCropModal()" title="Fermer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div class="crop-area">
            <img id="crop-img" src="" alt="">
        </div>

        <div class="crop-controls">
            <div class="crop-ctrl-row">
                <span class="crop-ctrl-label">Rogner</span>
                <button type="button" class="crop-icon-btn" onclick="cropperInst && cropperInst.rotate(-90)" title="Rotation gauche">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                </button>
                <button type="button" class="crop-icon-btn" onclick="cropperInst && cropperInst.rotate(90)" title="Rotation droite">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                </button>
                <button type="button" class="crop-icon-btn" onclick="cropperInst && cropperInst.scaleX(cropperInst.getData().scaleX === -1 ? 1 : -1)" title="Miroir horizontal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                </button>
            </div>
            <div class="crop-ctrl-row">
                <span class="crop-ctrl-label">Zoomer</span>
                <input type="range" class="crop-zoom-slider" id="crop-zoom-range"
                       min="0" max="100" value="50" oninput="onZoomSlider(this.value)">
            </div>
            <div class="crop-ctrl-row">
                <span class="crop-ctrl-label">Redresser</span>
                <input type="range" class="crop-zoom-slider" id="crop-rotate-range"
                       min="-45" max="45" value="0" oninput="cropperInst && cropperInst.rotateTo(this.value)">
            </div>
        </div>

        <div class="crop-actions">
            <button type="button" class="crop-btn-cancel" onclick="closeCropModal()">Annuler</button>
            <button type="button" class="crop-btn-save" onclick="applyCrop()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                Enregistrer la photo
            </button>
        </div>
    </div>
</div>

@endsection
