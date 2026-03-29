@extends('layouts.employee')

@push('styles')
<style>
.settings-grid {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 1.4rem;
    align-items: start;
}
@media(max-width:840px) { .settings-grid { grid-template-columns: 1fr; } }

/* ── Nav tabs (left) ── */
.settings-nav {
    background: #fff;
    border: 1.5px solid #dde8e8;
    border-radius: 16px;
    padding: 0.6rem;
    box-shadow: 0 2px 10px rgba(12,112,112,0.07);
}
.settings-nav-item {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.7rem 0.9rem;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 600;
    color: #6b8080;
    cursor: pointer;
    text-decoration: none;
    transition: 140ms ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}
.settings-nav-item:hover { background: #f4f9f9; color: #1e2c2c; }
.settings-nav-item.active { background: #e6f4f4; color: #0c7070; }
.settings-nav-item svg { flex-shrink: 0; }

/* ── Content panels (right) ── */
.settings-panel {
    background: #fff;
    border: 1.5px solid #dde8e8;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(12,112,112,0.07);
    display: none;
}
.settings-panel.active { display: block; }

.settings-panel-head {
    padding: 1.1rem 1.5rem;
    border-bottom: 1.5px solid #edf2f2;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.settings-panel-head-icon {
    width: 38px; height: 38px; border-radius: 11px;
    background: #e6f4f4;
    display: grid; place-items: center;
}
.settings-panel-head-title { font-size: 0.95rem; font-weight: 700; color: #1e2c2c; }
.settings-panel-head-sub   { font-size: 0.78rem; color: #6b8080; }

.settings-panel-body { padding: 1.4rem 1.5rem; }

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
@media(max-width:680px) { .form-row { grid-template-columns: 1fr; } }

.field { margin-bottom: 1rem; }
.field label {
    display: block;
    font-size: 0.84rem;
    font-weight: 700;
    color: #1e2c2c;
    margin-bottom: 0.35rem;
}
.field input {
    width: 100%;
    border: 1.5px solid #dde8e8;
    border-radius: 10px;
    padding: 0.6rem 0.9rem;
    font: inherit;
    font-size: 0.88rem;
    color: #1e2c2c;
    background: #fff;
    outline: none;
    transition: border-color 140ms;
}
.field input:focus { border-color: #0c7070; }
.field-hint { font-size: 0.74rem; color: #9bb0b0; margin-top: 0.3rem; }

/* Avatar upload zone */
.avatar-zone {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    margin-bottom: 1.2rem;
    padding: 1rem;
    border: 1.5px dashed #c8dcdc;
    border-radius: 14px;
    background: #f9fbfb;
    cursor: pointer;
    transition: 140ms ease;
}
.avatar-zone:hover { border-color: #0c7070; background: #f0f7f7; }
.avatar-zone-preview {
    width: 62px; height: 62px; border-radius: 50%;
    background: #e6f4f4;
    display: grid; place-items: center;
    flex-shrink: 0;
    overflow: hidden;
    font-size: 1.3rem; font-weight: 800; color: #0c7070;
}
.avatar-zone-info-title { font-size: 0.85rem; font-weight: 700; color: #1e2c2c; }
.avatar-zone-info-sub   { font-size: 0.75rem; color: #9bb0b0; margin-top: 2px; }

/* Save button row */
.save-row {
    display: flex;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1.5px solid #edf2f2;
    margin-top: 0.5rem;
}
.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.58rem 1.3rem;
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #0c7070, #14a8a8);
    color: #fff;
    font: inherit;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    transition: 140ms ease;
    box-shadow: 0 4px 14px rgba(12,112,112,0.22);
}
.btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(12,112,112,0.3); }
</style>
@endpush

@section('content')

<div style="margin-bottom:1.2rem;">
    <div style="font-size:1.2rem;font-weight:800;color:#1e2c2c;">Paramètres</div>
    <div style="font-size:0.82rem;color:#6b8080;margin-top:2px;">Gérez vos informations personnelles et vos préférences</div>
</div>

<div class="settings-grid">

    {{-- Left nav --}}
    <div class="settings-nav">
        <button class="settings-nav-item active" onclick="showTab('profile', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            Profil
        </button>
        <button class="settings-nav-item" onclick="showTab('password', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Mot de passe
        </button>
        <button class="settings-nav-item" onclick="showTab('location', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
            Localisation
        </button>
    </div>

    {{-- Right panels --}}
    <div>

        {{-- Profile panel --}}
        <div class="settings-panel active" id="tab-profile">
            <div class="settings-panel-head">
                <div class="settings-panel-head-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0c7070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div>
                    <div class="settings-panel-head-title">Informations personnelles</div>
                    <div class="settings-panel-head-sub">Modifiez votre nom d'affichage et votre photo de profil</div>
                </div>
            </div>
            <div class="settings-panel-body">
                <form method="POST" action="{{ route('employee.settings.profile') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Avatar --}}
                    <div class="avatar-zone" onclick="document.getElementById('avatar-input').click()">
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none;"
                               onchange="previewAvatar(this)">
                        <div class="avatar-zone-preview" id="avatar-preview">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                {{ strtoupper(substr(auth()->user()->name,0,2)) }}
                            @endif
                        </div>
                        <div>
                            <div class="avatar-zone-info-title">Changer la photo de profil</div>
                            <div class="avatar-zone-info-sub">JPG, PNG · max 2 Mo · Cliquez pour choisir</div>
                        </div>
                    </div>

                    <div class="field">
                        <label for="name">Nom d'utilisateur</label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', auth()->user()->name) }}"
                               required maxlength="100">
                        <div class="field-hint">Ce nom est visible par les autres utilisateurs.</div>
                    </div>

                    <div class="save-row">
                        <button type="submit" class="btn-save">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Enregistrer le profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Password panel --}}
        <div class="settings-panel" id="tab-password">
            <div class="settings-panel-head">
                <div class="settings-panel-head-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0c7070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </div>
                <div>
                    <div class="settings-panel-head-title">Mot de passe</div>
                    <div class="settings-panel-head-sub">Modifiez votre mot de passe de connexion</div>
                </div>
            </div>
            <div class="settings-panel-body">
                <form method="POST" action="{{ route('employee.settings.password') }}">
                    @csrf

                    <div class="field">
                        <label for="current_password">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-row">
                        <div class="field">
                            <label for="password">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" required minlength="8">
                            <div class="field-hint">Minimum 8 caractères</div>
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="save-row">
                        <button type="submit" class="btn-save">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Location panel --}}
        <div class="settings-panel" id="tab-location">
            <div class="settings-panel-head">
                <div class="settings-panel-head-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0c7070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div>
                    <div class="settings-panel-head-title">Localisation</div>
                    <div class="settings-panel-head-sub">Indiquez votre lieu de travail ou votre agence</div>
                </div>
            </div>
            <div class="settings-panel-body">
                <form method="POST" action="{{ route('employee.settings.location') }}">
                    @csrf

                    <div class="field">
                        <label for="location">Localisation / Agence</label>
                        <input type="text" id="location" name="location"
                               value="{{ old('location', auth()->user()->location) }}"
                               maxlength="150"
                               placeholder="Ex : Paris, Siège social — Bâtiment A">
                        <div class="field-hint">Votre ville, site ou agence de rattachement.</div>
                    </div>

                    <div class="save-row">
                        <button type="submit" class="btn-save">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            Enregistrer la localisation
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function showTab(name, btn) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.settings-nav-item').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatar-preview').innerHTML =
            '<img src="' + e.target.result + '" alt="avatar" style="width:100%;height:100%;object-fit:cover;">';
    };
    reader.readAsDataURL(input.files[0]);
}

// Open the right tab if there were validation errors
@if($errors->hasAny(['current_password', 'password']))
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.settings-nav-item')[1].click();
    });
@elseif($errors->has('location'))
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.settings-nav-item')[2].click();
    });
@endif
</script>
@endpush
