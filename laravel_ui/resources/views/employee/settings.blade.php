@extends('layouts.employee')

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
    background:#fff; border-radius:20px; width:min(96vw,700px);
    box-shadow:0 24px 64px rgba(0,0,0,.35); overflow:hidden;
    display:flex; flex-direction:column; max-height:92vh;
}
.crop-modal-header {
    padding:1.1rem 1.5rem; border-bottom:1.5px solid #edf2f2;
    display:flex; align-items:center; justify-content:space-between;
}
.crop-modal-title { font-size:1rem; font-weight:800; color:#1e2c2c; }
.crop-modal-close {
    width:32px; height:32px; border-radius:50%;
    background:#f0f4f4; border:none; cursor:pointer;
    display:grid; place-items:center; color:#4a7070; transition:background 140ms;
}
.crop-modal-close:hover { background:#e0ecec; }
.crop-area {
    background:#1a1a1a; flex:1; overflow:hidden; max-height:55vh;
    display:flex; align-items:center; justify-content:center;
}
.crop-area img { max-width:100%; display:block; }
.crop-controls {
    padding:1rem 1.5rem; border-top:1.5px solid #edf2f2;
    display:flex; flex-direction:column; gap:.9rem;
}
.crop-ctrl-row { display:flex; align-items:center; gap:1rem; }
.crop-ctrl-label { font-size:.78rem; font-weight:700; color:#4a7070; min-width:72px; }
.crop-icon-btn {
    width:36px; height:36px; border-radius:10px;
    background:#f0f7f7; border:1.5px solid #d0e2e2;
    cursor:pointer; display:grid; place-items:center;
    color:#0c7070; transition:background 130ms;
}
.crop-icon-btn:hover { background:#d5f0f0; }
.crop-zoom-slider { flex:1; accent-color:#0c7070; height:4px; cursor:pointer; }
.crop-actions {
    display:flex; align-items:center; justify-content:flex-end;
    gap:.7rem; padding:1rem 1.5rem; border-top:1.5px solid #edf2f2;
}
.crop-btn-cancel {
    padding:.5rem 1.1rem; border-radius:10px;
    background:#f0f4f4; border:1.5px solid #d0e2e2;
    font-size:.85rem; font-weight:600; color:#4a7070; cursor:pointer;
}
.crop-btn-cancel:hover { background:#e4ecec; }
.crop-btn-save {
    padding:.5rem 1.4rem; border-radius:10px;
    background:linear-gradient(135deg,var(--teal),var(--teal-mid));
    border:none; font-size:.85rem; font-weight:700; color:#fff;
    cursor:pointer; box-shadow:0 3px 10px var(--teal-shadow); transition:opacity 140ms;
}
.crop-btn-save:hover { opacity:.88; }

/* ── Hero banner ── */
.settings-hero {
    background: linear-gradient(135deg, var(--teal) 0%, var(--teal-mid) 55%, var(--teal-light) 100%);
    border-radius: 20px; padding: 2rem 2.2rem;
    display: flex; align-items: center; gap: 1.8rem;
    margin-bottom: 1.6rem; position: relative; overflow: hidden;
    box-shadow: 0 8px 32px var(--teal-shadow);
}
.settings-hero::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:240px; height:240px; border-radius:50%; background:rgba(255,255,255,.07);
}
.settings-hero::after {
    content:''; position:absolute; bottom:-70px; right:90px;
    width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.05);
}
.hero-avatar-wrap { position:relative; flex-shrink:0; cursor:pointer; }
.hero-avatar {
    width:88px; height:88px; border-radius:50%;
    background:rgba(255,255,255,.18); border:3px solid rgba(255,255,255,.45);
    display:grid; place-items:center; overflow:hidden;
    font-size:2rem; font-weight:800; color:#fff; transition:filter 200ms;
}
.hero-avatar img { width:100%; height:100%; object-fit:cover; }
.hero-avatar-overlay {
    position:absolute; inset:0; border-radius:50%;
    background:rgba(0,0,0,.35); display:grid; place-items:center;
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
    font-size:.75rem; font-weight:600; color:rgba(255,255,255,.9); backdrop-filter:blur(4px);
}
.hero-edit-btn {
    z-index:1; background:rgba(255,255,255,.15); border:1.5px solid rgba(255,255,255,.3);
    border-radius:10px; padding:.55rem 1.1rem;
    font:inherit; font-size:.84rem; font-weight:600; color:#fff;
    cursor:pointer; display:flex; align-items:center; gap:.5rem;
    transition:background 160ms; text-decoration:none;
}
.hero-edit-btn:hover { background:rgba(255,255,255,.25); }

/* ── Layout ── */
.settings-layout {
    display: grid; grid-template-columns: 240px 1fr;
    gap: 1.4rem; align-items: start;
}

/* ── Sidebar nav ── */
.settings-nav {
    background:#fff; border:1.5px solid #dde8e8; border-radius:18px;
    padding:.5rem; display:flex; flex-direction:column; gap:.15rem;
    position:sticky; top:1.5rem; overflow:hidden;
}
.snav-label {
    font-size:.68rem; font-weight:700; letter-spacing:.08em;
    color:#9bb0b0; text-transform:uppercase; padding:.65rem .9rem .3rem;
}
.snav-item {
    display:flex; align-items:center; gap:.85rem; padding:.72rem 1rem; border-radius:12px;
    background:none; border:none; cursor:pointer; font:inherit; text-align:left; width:100%;
    transition:background 130ms, color 130ms; position:relative; text-decoration:none;
}
.snav-icon {
    width:36px; height:36px; border-radius:10px;
    display:grid; place-items:center; flex-shrink:0; transition:transform 150ms;
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
    width:3px; border-radius:0 3px 3px 0; background:#0c7070;
}
.snav-divider { height:1px; background:#edf2f2; margin:.3rem .9rem; }

/* ── Panels ── */
.settings-panel { display:none; flex-direction:column; gap:1.3rem; }
.settings-panel.active { display:flex; }

/* ── Cards ── */
.scard {
    background:#fff; border:1.5px solid #dde8e8; border-radius:18px;
    overflow:hidden; box-shadow:0 2px 12px rgba(12,112,112,.06); transition:box-shadow 200ms;
}
.scard:hover { box-shadow:0 4px 20px rgba(12,112,112,.10); }
.scard-head {
    padding:1.2rem 1.6rem; border-bottom:1px solid #edf2f2;
    display:flex; align-items:center; gap:.9rem; position:relative; overflow:hidden;
}
.scard-head::after { content:''; position:absolute; bottom:0; left:0; right:0; height:2px; }
.scard-head.teal::after   { background:linear-gradient(90deg,var(--teal),var(--teal-light)); }
.scard-head.gold::after   { background:linear-gradient(90deg,#d4a017,#f5c842); }
.scard-head.green::after  { background:linear-gradient(90deg,#16a34a,#4ade80); }
.scard-head.purple::after { background:linear-gradient(90deg,#9333ea,#c084fc); }
.scard-hicon { width:42px; height:42px; border-radius:12px; display:grid; place-items:center; flex-shrink:0; }
.scard-htitle { font-weight:700; font-size:1rem; color:#1e2c2c; }
.scard-hsub   { font-size:.78rem; color:#6b8080; margin-top:2px; }
.scard-body   { padding:1.5rem 1.6rem; display:flex; flex-direction:column; gap:1.15rem; }

/* ── Avatar section ── */
.avatar-section {
    display:flex; align-items:center; gap:1.4rem; padding:1.3rem 1.5rem;
    background:linear-gradient(135deg,#f4fbfb,#e8f7f7); border:1.5px dashed #a0cccc;
    border-radius:14px; cursor:pointer; transition:border-color 150ms, background 150ms; position:relative;
}
.avatar-section:hover { border-color:var(--teal); background:linear-gradient(135deg,#e8f7f7,#d5f0f0); }
.av-ring {
    width:80px; height:80px; border-radius:50%; display:grid; place-items:center;
    flex-shrink:0; overflow:hidden; background:linear-gradient(135deg,var(--teal),var(--teal-mid));
    border:3px solid #fff; box-shadow:0 4px 14px var(--teal-shadow);
    font-size:1.6rem; font-weight:800; color:#fff; position:relative;
}
.av-ring img { width:100%; height:100%; object-fit:cover; }
.av-ring-overlay {
    position:absolute; inset:0; border-radius:50%;
    background:rgba(0,0,0,.38); display:grid; place-items:center;
    opacity:0; transition:opacity 160ms;
}
.avatar-section:hover .av-ring-overlay { opacity:1; }
.av-info-title { font-size:.9rem; font-weight:700; color:#1e2c2c; }
.av-info-sub   { font-size:.76rem; color:#6b8080; margin-top:3px; }
.av-btn {
    margin-left:auto; background:#fff; border:1.5px solid #c5dede; border-radius:10px;
    padding:.5rem 1rem; font:inherit; font-size:.82rem; font-weight:600; color:var(--teal);
    cursor:pointer; display:flex; align-items:center; gap:.4rem;
    transition:background 140ms, border-color 140ms; white-space:nowrap;
}
.av-btn:hover { background:var(--teal-soft); border-color:var(--teal); }

/* ── Form elements ── */
.form-row  { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.form-group { display:flex; flex-direction:column; gap:.32rem; }
.form-group label { font-size:.82rem; font-weight:600; color:#2c4040; letter-spacing:.01em; }
.form-group input {
    width:100%; border:1.5px solid #d0e2e2; border-radius:10px;
    padding:.6rem .9rem; font:inherit; font-size:.88rem; color:#1e2c2c;
    background:#fafefe; transition:border-color 150ms, box-shadow 150ms; outline:none;
}
.form-group input:focus { border-color:var(--teal); box-shadow:0 0 0 3px var(--teal-shadow); background:#fff; }
.form-hint { font-size:.74rem; color:#6b8080; margin-top:2px; }

/* ── Password ── */
.pw-wrap { position:relative; }
.pw-wrap input { padding-right:2.8rem; }
.pw-toggle {
    position:absolute; right:.7rem; top:50%; transform:translateY(-50%);
    background:none; border:none; cursor:pointer; color:#7a9a9a;
    padding:.2rem; display:grid; place-items:center; transition:color 140ms;
}
.pw-toggle:hover { color:#0c7070; }
.pw-strength { display:flex; flex-direction:column; gap:.35rem; }
.pw-strength-bars { display:flex; gap:.3rem; }
.pw-bar { flex:1; height:4px; border-radius:99px; background:#e2eaea; transition:background .3s; }
.pw-strength-label { font-size:.74rem; font-weight:600; color:#9bb0b0; }

/* ── Info band ── */
.info-band {
    background:var(--teal-soft); border:1.5px solid #c8e8e8; border-radius:10px;
    padding:.7rem 1rem; display:flex; align-items:center; gap:.6rem;
    font-size:.8rem; color:var(--teal);
}

/* ── Language cards ── */
.lang-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.lang-card {
    position:relative; display:flex; flex-direction:column; align-items:center; gap:.6rem;
    padding:1.4rem 1rem; border:2px solid #dde8e8; border-radius:14px;
    cursor:pointer; transition:150ms ease; background:#fff;
}
.lang-card:hover { border-color:#14a8a8; background:#f4fafa; }
.lang-card.selected { border-color:#0c7070; background:#e8f6f6; }
.lang-card input[type=radio] { position:absolute; opacity:0; pointer-events:none; }
.lang-flag { font-size:2.2rem; line-height:1; }
.lang-label { font-size:.9rem; font-weight:700; color:#1e2c2c; }
.lang-sub   { font-size:.74rem; color:#6b8080; }
.lang-check {
    width:20px; height:20px; border-radius:50%; border:2px solid #c4d8d8;
    display:grid; place-items:center; margin-top:.2rem; transition:150ms ease;
}
.lang-card.selected .lang-check { background:#0c7070; border-color:#0c7070; }
.lang-card.selected .lang-check::after {
    content:''; width:6px; height:6px; border-radius:50%; background:#fff;
}

/* ── Actions ── */
.form-actions { display:flex; justify-content:flex-end; gap:.65rem; padding-top:.3rem; }
.btn-save {
    display:inline-flex; align-items:center; gap:.5rem;
    background:linear-gradient(135deg,var(--teal),var(--teal-mid));
    color:#fff; border:none; border-radius:10px; padding:.65rem 1.4rem;
    font:inherit; font-size:.88rem; font-weight:700;
    cursor:pointer; transition:opacity 150ms, transform 120ms;
    box-shadow:0 3px 10px var(--teal-shadow);
}
.btn-save:hover { opacity:.9; transform:translateY(-1px); }
.btn-save:active { transform:translateY(0); }
.btn-cancel {
    display:inline-flex; align-items:center; gap:.5rem;
    background:#fff; color:#4a6666; border:1.5px solid #d0e2e2; border-radius:10px;
    padding:.63rem 1.2rem; font:inherit; font-size:.88rem; font-weight:600;
    cursor:pointer; transition:background 140ms, border-color 140ms;
}
.btn-cancel:hover { background:#f4fbfb; border-color:#a0cccc; }

@media (max-width:860px) {
    .settings-layout { grid-template-columns:1fr; }
    .settings-nav { flex-direction:row; flex-wrap:wrap; position:static; gap:.15rem; }
    .snav-desc { display:none; }
    .snav-item.active::before { display:none; }
    .form-row { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')

{{-- ── Hero Banner ── --}}
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
        <div class="hero-role">{{ auth()->user()->role === 'supervisor' ? __('Supervisor') : __('Employee') }}</div>
        <div class="hero-badges">
            <span class="hero-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ auth()->user()->timezone ?? 'Africa/Algiers' }}
            </span>
            <span class="hero-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                {{ strtoupper(session('locale', config('app.locale', 'fr'))) }}
            </span>
        </div>
    </div>

    <button type="button" class="hero-edit-btn" onclick="switchTab('profile', document.querySelector('[data-tab=profile]'))">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        {{ __('Edit profile') }}
    </button>
</div>

{{-- ── Grid Layout ── --}}
<div class="settings-layout">

    {{-- ── Left Nav ── --}}
    <nav class="settings-nav">
        <div class="snav-label">{{ __('My account') }}</div>

        <button class="snav-item active" data-tab="profile" onclick="switchTab('profile',this)" type="button">
            <div class="snav-icon" style="background:#e6f4f4;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0c7070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="snav-text">
                <span class="snav-title">{{ __('Profile') }}</span>
                <span class="snav-desc">{{ __('Name & photo') }}</span>
            </div>
        </button>

        <button class="snav-item" data-tab="password" onclick="switchTab('password',this)" type="button">
            <div class="snav-icon" style="background:#fef9ec;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d4a017" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
            <div class="snav-text">
                <span class="snav-title">{{ __('Password') }}</span>
                <span class="snav-desc">{{ __('Account security') }}</span>
            </div>
        </button>

        <div class="snav-divider"></div>
        <div class="snav-label">{{ __('Preferences') }}</div>

        <button class="snav-item" data-tab="timezone" onclick="switchTab('timezone',this)" type="button">
            <div class="snav-icon" style="background:#ecfdf5;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="snav-text">
                <span class="snav-title">{{ __('Timezone') }}</span>
                <span class="snav-desc">{{ auth()->user()->timezone ?? 'Africa/Algiers' }}</span>
            </div>
        </button>

        <button class="snav-item" data-tab="language" onclick="switchTab('language',this)" type="button">
            <div class="snav-icon" style="background:#fdf4ff;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9333ea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
            </div>
            <div class="snav-text">
                <span class="snav-title">{{ __('Language') }}</span>
                <span class="snav-desc">{{ __('Display language') }}</span>
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
                        <div class="scard-htitle">{{ __('Profile information') }}</div>
                        <div class="scard-hsub">{{ __('Profile picture and display name') }}</div>
                    </div>
                </div>
                <div class="scard-body">
                    <form method="POST" action="{{ route('employee.settings.profile') }}" enctype="multipart/form-data" id="profile-form">
                        @csrf

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
                                <div class="av-info-title">{{ __('Change profile picture') }}</div>
                                <div class="av-info-sub">{{ __('JPG, PNG or GIF · max 2 MB') }}</div>
                            </div>
                            <div class="av-btn">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.29"/></svg>
                                {{ __('Browse') }}
                            </div>
                            <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none;" onchange="previewAvatar(this)">
                        </label>

                        <div class="form-group">
                            <label for="name">{{ __('Display name') }}</label>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   placeholder="{{ __('Your full name') }}" required>
                            @error('name')
                                <div class="form-hint" style="color:#dc2626;">{{ $message }}</div>
                            @enderror
                        </div>

                      

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="document.getElementById('name').value='{{ addslashes(auth()->user()->name) }}'">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn-save">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                {{ __('Save') }}
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
                        <div class="scard-htitle">{{ __('Change password') }}</div>
                        <div class="scard-hsub">{{ __('Choose a strong password (min. 8 characters)') }}</div>
                    </div>
                </div>
                <div class="scard-body">
                    <form method="POST" action="{{ route('employee.settings.password') }}">
                        @csrf

                        <div class="form-group">
                            <label for="current_password">{{ __('Current password') }}</label>
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
                                <label for="password">{{ __('New password') }}</label>
                                <div class="pw-wrap">
                                    <input type="password" id="password" name="password" placeholder="••••••••" required
                                           oninput="checkStrength(this.value)">
                                    <button class="pw-toggle" type="button" onclick="togglePw('password',this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">{{ __('Confirm password') }}</label>
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
                                {{ __('Update password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════ TIMEZONE ═══════ --}}
        <div class="settings-panel" id="panel-timezone">
            <div class="scard">
                <div class="scard-head green">
                    <div class="scard-hicon" style="background:#ecfdf5;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div class="scard-htitle">{{ __('Timezone') }}</div>
                        <div class="scard-hsub">{{ __('Local time displayed in the application') }}</div>
                    </div>
                </div>
                <div class="scard-body">
                    <form method="POST" action="{{ route('employee.settings.timezone') }}">
                        @csrf

                        <div class="form-group">
                            <label for="timezone">{{ __('Timezone') }}</label>
                            <select name="timezone" id="timezone" style="width:100%;border:1.5px solid #d0e2e2;border-radius:10px;padding:.6rem .9rem;font:inherit;font-size:.88rem;color:#1e2c2c;background:#fafefe;outline:none;">
                                @foreach($timezones as $tz)
                                    <option value="{{ $tz }}" {{ (auth()->user()->timezone ?? 'Africa/Algiers') === $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="form-hint" style="color:#dc2626;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════ LANGUAGE ═══════ --}}
        <div class="settings-panel" id="panel-language">
            <div class="scard">
                <div class="scard-head purple">
                    <div class="scard-hicon" style="background:#fdf4ff;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9333ea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    </div>
                    <div>
                        <div class="scard-htitle">{{ __('Interface language') }}</div>
                        <div class="scard-hsub">{{ __('Choose the display language of the application') }}</div>
                    </div>
                </div>
                <div class="scard-body">
                    <form method="POST" action="{{ route('employee.settings.language') }}">
                        @csrf
                        @php $currentLocale = session('locale', config('app.locale', 'fr')); @endphp

                        <div class="lang-grid">
                            <label class="lang-card {{ $currentLocale === 'fr' ? 'selected' : '' }}" id="lang-card-fr" onclick="selectLang('fr')">
                                <input type="radio" name="locale" value="fr" {{ $currentLocale === 'fr' ? 'checked' : '' }}>
                                <span class="lang-flag">🇫🇷</span>
                                <span class="lang-label">Français</span>
                                <span class="lang-sub">French</span>
                                <span class="lang-check"></span>
                            </label>
                            <label class="lang-card {{ $currentLocale === 'en' ? 'selected' : '' }}" id="lang-card-en" onclick="selectLang('en')">
                                <input type="radio" name="locale" value="en" {{ $currentLocale === 'en' ? 'checked' : '' }}>
                                <span class="lang-flag">🇬🇧</span>
                                <span class="lang-label">English</span>
                                <span class="lang-sub">Anglais</span>
                                <span class="lang-check"></span>
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                                {{ __('Save language') }}
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
    const target = (btn && btn.dataset && btn.dataset.tab) ? btn : document.querySelector('[data-tab=' + name + ']');
    if (target) target.classList.add('active');
}

/* ── Crop Modal ── */
let cropperInst  = null;
let cropCallback = null;

function openCropModal(file, callback) {
    cropCallback = callback;
    const reader = new FileReader();
    reader.onload = e => {
        const img     = document.getElementById('crop-img');
        const overlay = document.getElementById('crop-modal-overlay');
        img.src = e.target.result;
        overlay.classList.add('open');
        document.getElementById('crop-zoom-range').value   = 50;
        document.getElementById('crop-rotate-range').value = 0;
        if (cropperInst) { cropperInst.destroy(); cropperInst = null; }
        cropperInst = new Cropper(img, {
            aspectRatio: 1, viewMode: 1, dragMode: 'move', autoCropArea: 0.85,
            restore: false, guides: false, center: true, highlight: false,
            cropBoxMovable: false, cropBoxResizable: false, toggleDragModeOnDblclick: false,
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
    cropperInst.zoomTo(Math.max(0.1, 1 + (val - 50) / 50 * 1.5));
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

function syncHeroAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0]; input.value = '';
    openCropModal(file, (dataURL, blob) => {
        const heroHtml    = '<img src="' + dataURL + '" alt="avatar" style="width:100%;height:100%;object-fit:cover;">';
        const overlayHtml = '<div class="av-ring-overlay"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg></div>';
        document.getElementById('hero-avatar').innerHTML = heroHtml;
        const avRing = document.getElementById('av-ring');
        if (avRing) avRing.innerHTML = heroHtml + overlayHtml;
        const croppedFile = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer(); dt.items.add(croppedFile);
        document.getElementById('avatar-input').files = dt.files;
    });
}

function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0]; input.value = '';
    openCropModal(file, (dataURL, blob) => {
        const heroHtml    = '<img src="' + dataURL + '" alt="avatar" style="width:100%;height:100%;object-fit:cover;">';
        const overlayHtml = '<div class="av-ring-overlay"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg></div>';
        document.getElementById('av-ring').innerHTML  = heroHtml + overlayHtml;
        document.getElementById('hero-avatar').innerHTML = heroHtml;
        const croppedFile = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer(); dt.items.add(croppedFile);
        input.files = dt.files;
    });
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
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Très faible','Faible','Moyen','Fort'];
    for (let i = 1; i <= 4; i++) {
        document.getElementById('pw-b' + i).style.background = i <= score ? colors[score - 1] : '#e2eaea';
    }
    document.getElementById('pw-strength-label').textContent = labels[score - 1] || 'Très faible';
    document.getElementById('pw-strength-label').style.color = colors[score - 1] || colors[0];
}

/* ── Language card toggle ── */
function selectLang(code) {
    ['fr','en'].forEach(c => {
        document.getElementById('lang-card-' + c).classList.toggle('selected', c === code);
        const radio = document.querySelector('#lang-card-' + c + ' input[type=radio]');
        if (radio) radio.checked = (c === code);
    });
}

/* ── Auto-open correct tab on validation errors ── */
@if($errors->hasAny(['current_password', 'password']))
    document.addEventListener('DOMContentLoaded', () => switchTab('password', null));
@elseif($errors->has('timezone'))
    document.addEventListener('DOMContentLoaded', () => switchTab('timezone', null));
@elseif(session('_lang_tab'))
    document.addEventListener('DOMContentLoaded', () => switchTab('language', null));
@endif
</script>
@endpush

{{-- ── Avatar Crop Modal ── --}}
<div class="crop-modal-overlay" id="crop-modal-overlay">
    <div class="crop-modal">
        <div class="crop-modal-header">
            <span class="crop-modal-title">Modifier la photo</span>
            <button class="crop-modal-close" type="button" onclick="closeCropModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="crop-area"><img id="crop-img" src="" alt=""></div>
        <div class="crop-controls">
            <div class="crop-ctrl-row">
                <span class="crop-ctrl-label">Rogner</span>
                <button type="button" class="crop-icon-btn" onclick="cropperInst && cropperInst.rotate(-90)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                </button>
                <button type="button" class="crop-icon-btn" onclick="cropperInst && cropperInst.rotate(90)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                </button>
                <button type="button" class="crop-icon-btn" onclick="cropperInst && cropperInst.scaleX(cropperInst.getData().scaleX === -1 ? 1 : -1)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                </button>
            </div>
            <div class="crop-ctrl-row">
                <span class="crop-ctrl-label">Zoomer</span>
                <input type="range" class="crop-zoom-slider" id="crop-zoom-range" min="0" max="100" value="50" oninput="onZoomSlider(this.value)">
            </div>
            <div class="crop-ctrl-row">
                <span class="crop-ctrl-label">Redresser</span>
                <input type="range" class="crop-zoom-slider" id="crop-rotate-range" min="-45" max="45" value="0" oninput="cropperInst && cropperInst.rotateTo(this.value)">
            </div>
        </div>
        <div class="crop-actions">
            <button type="button" class="crop-btn-cancel" onclick="closeCropModal()">{{ __('Cancel') }}</button>
            <button type="button" class="crop-btn-save" onclick="applyCrop()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                {{ __('Save photo') }}
            </button>
        </div>
    </div>
</div>

@endsection
