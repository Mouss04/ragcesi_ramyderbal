@extends('layouts.admin')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.4rem;
    }
    .page-header-title { font-size: 1.3rem; font-weight: 800; color: #1e2c2c; }
    .page-header-sub   { font-size: 0.83rem; color: #6b8080; margin-top: 2px; }

    /* stat mini-cards */
    .user-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.4rem;
    }
    .user-stat {
        background: #fff;
        border: 1.5px solid #dde8e8;
        border-radius: 14px;
        padding: 1rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }
    .user-stat-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .user-stat-val   { font-size: 1.6rem; font-weight: 800; color: #1e2c2c; line-height: 1; }
    .user-stat-label { font-size: 0.75rem; color: #6b8080; margin-top: 2px; }

    /* user cards grid */
    .user-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1rem;
    }
    .user-card {
        background: #fff;
        border: 1.5px solid #dde8e8;
        border-radius: 16px;
        padding: 1.3rem 1.2rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.5rem;
        transition: box-shadow 160ms, transform 160ms;
        position: relative;
        overflow: hidden;
    }
    .user-card:hover {
        box-shadow: 0 8px 24px rgba(12,112,112,0.12);
        transform: translateY(-2px);
    }
    .user-card-stripe {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
    }
    .user-card-avatar {
        width: 56px; height: 56px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 1.3rem;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
        margin-top: 0.3rem;
    }
    .user-card-name  { font-weight: 700; font-size: 0.95rem; color: #1e2c2c; }
    .user-card-id    { font-size: 0.73rem; color: #6b8080; }
    .user-card-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0.2rem 0.65rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .badge-admin       { background: #fef3c7; color: #92400e; }
    .badge-supervisor  { background: #ede9fe; color: #5b21b6; }
    .badge-user        { background: #e6f4f4; color: #0c7070; }
    .user-card-date { font-size: 0.72rem; color: #9bb0b0; }
    .user-card-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.6rem;
        width: 100%;
    }
    .user-card-actions .btn { flex: 1; font-size: 0.8rem; padding: 0.45rem 0.6rem; }

    /* empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b8080;
    }
    .empty-state svg { opacity: 0.25; margin-bottom: 0.8rem; }
    .empty-state p { font-size: 0.9rem; }

    /* ── Edit modal ── */
    .edit-overlay {
        display: none; position: fixed; inset: 0; z-index: 9000;
        background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
        align-items: center; justify-content: center;
    }
    .edit-overlay.open { display: flex; }
    .edit-modal {
        background: #fff; border-radius: 20px;
        width: min(96vw, 480px);
        box-shadow: 0 24px 64px rgba(0,0,0,.3);
        overflow: hidden;
    }
    .edit-modal-header {
        background: linear-gradient(135deg, var(--teal), var(--teal-mid));
        padding: 1.2rem 1.5rem;
        display: flex; align-items: center; gap: 1rem;
    }
    .edit-modal-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        background: rgba(255,255,255,.2); border: 2px solid rgba(255,255,255,.35);
        display: grid; place-items: center;
        font-size: 1rem; font-weight: 800; color: #fff; flex-shrink: 0;
    }
    .edit-modal-htitle { font-size: 1rem; font-weight: 800; color: #fff; }
    .edit-modal-hsub   { font-size: .75rem; color: rgba(255,255,255,.7); margin-top: 2px; }
    .edit-modal-close {
        margin-left: auto; width: 32px; height: 32px; border-radius: 50%;
        background: rgba(255,255,255,.15); border: none; cursor: pointer;
        display: grid; place-items: center; color: #fff;
        transition: background 140ms;
    }
    .edit-modal-close:hover { background: rgba(255,255,255,.28); }
    .edit-modal-body { padding: 1.4rem 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
    .edit-field label {
        display: block; font-size: .8rem; font-weight: 700;
        color: #3a5858; margin-bottom: .4rem;
    }
    .edit-field input,
    .edit-field select {
        width: 100%; border: 1.5px solid #d0e2e2; border-radius: 11px;
        padding: .62rem .9rem; font: inherit; font-size: .88rem;
        color: #1e2c2c; background: #fafefe; outline: none;
        transition: border-color 150ms, box-shadow 150ms;
        box-sizing: border-box;
    }
    .edit-field input:focus,
    .edit-field select:focus {
        border-color: var(--teal);
        box-shadow: 0 0 0 3px var(--teal-shadow);
        background: #fff;
    }
    .edit-field-hint { font-size: .73rem; color: #9bb0b0; margin-top: .3rem; }
    .edit-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1.5px solid #edf2f2;
        display: flex; justify-content: flex-end; gap: .7rem;
    }
    .edit-btn-cancel {
        padding: .5rem 1.1rem; border-radius: 10px;
        background: #f0f4f4; border: 1.5px solid #d0e2e2;
        font-size: .85rem; font-weight: 600; color: #4a7070; cursor: pointer;
    }
    .edit-btn-cancel:hover { background: #e4ecec; }
    .edit-btn-save {
        padding: .5rem 1.4rem; border-radius: 10px;
        background: linear-gradient(135deg, var(--teal), var(--teal-mid));
        border: none; font-size: .85rem; font-weight: 700; color: #fff;
        cursor: pointer; box-shadow: 0 3px 10px var(--teal-shadow);
        display: flex; align-items: center; gap: .4rem;
        transition: opacity 140ms;
    }
    .edit-btn-save:hover { opacity: .88; }

    /* ── Avatar mini-drop ── */
    .modal-avatar-drop {
        display: flex; align-items: center; gap: 1rem;
        padding: .8rem;
        border: 2px dashed #c0d8d8;
        border-radius: 12px;
        cursor: pointer;
        background: #fafefe;
        transition: border-color 140ms, background 140ms;
        position: relative;
    }
    .modal-avatar-drop:hover { border-color: var(--teal); background: var(--teal-soft); }
    .modal-avatar-drop input[type=file] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .modal-av-preview {
        width: 52px; height: 52px; border-radius: 50%;
        object-fit: cover; flex-shrink: 0;
        display: grid; place-items: center;
        font-size: 1.1rem; font-weight: 800; color: #fff;
        overflow: hidden;
    }
    .modal-av-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .modal-av-info-title { font-size: .82rem; font-weight: 700; color: #2a4848; }
    .modal-av-info-sub   { font-size: .73rem; color: #7a9898; margin-top: 2px; }
</style>
@endpush

@section('content')

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="page-header-title">{{ __('User management') }}</div>
            <div class="page-header-sub">{{ __('Create, edit and delete employee and administrator accounts.') }}</div>
        </div>
        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ __('New user') }}
        </button>
    </div>

    {{-- Mini stats --}}
    <div class="user-stats">
        <div class="user-stat">
            <div class="user-stat-icon" style="background:#e6f4f4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0c7070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div>
                <div class="user-stat-val">{{ $users->count() }}</div>
                <div class="user-stat-label">{{ __('Total users') }}</div>
            </div>
        </div>
        <div class="user-stat">
            <div class="user-stat-icon" style="background:#fef9ec;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d4a017" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div>
                <div class="user-stat-val">{{ $users->where('role','admin')->count() }}</div>
                <div class="user-stat-label">{{ __('Administrators') }}</div>
            </div>
        </div>
        <div class="user-stat">
                <div class="user-stat-icon" style="background:#ede9fe;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#5b21b6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div>
                <div class="user-stat-val">{{ $users->where('role','supervisor')->count() }}</div>
                <div class="user-stat-label">{{ __('Supervisors') }}</div>
            </div>
        </div>
    </div>

    {{-- User cards --}}
    @if($users->isEmpty())
        <div class="empty-state">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#6b8080" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <p>{{ __('No user found.') }}</p>
        </div>
    @else
        <div class="user-grid">
            @foreach($users as $user)
                @php
                    $isAdmin      = $user->role === 'admin';
                    $isSupervisor = $user->role === 'supervisor';
                    $colors  = ['#0c7070','#6366f1','#d4a017','#e11d48','#0891b2','#16a34a'];
                    $bg      = $colors[$user->id % count($colors)];
                    $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                    $stripe = $isAdmin ? 'linear-gradient(90deg,#d4a017,#f5cb5c)' : ($isSupervisor ? 'linear-gradient(90deg,#7c3aed,#a78bfa)' : 'linear-gradient(90deg,#0c7070,#14a8a8)');
                @endphp
                <div class="user-card">
                    <div class="user-card-stripe" style="background: {{ $stripe }};"></div>
                <div class="user-card-avatar" style="background: {{ $bg }}; {{ $user->avatar ? 'padding:0;overflow:hidden;' : '' }}">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                    <div class="user-card-name">{{ $user->name }}</div>
                    <div class="user-card-id">#{{ $user->id }}</div>
                    @if($isAdmin)
                        <span class="user-card-badge badge-admin">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            {{ __('Administrator') }}
                        </span>
                    @elseif($isSupervisor)
                        <span class="user-card-badge badge-supervisor">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            {{ __('Supervisor') }}
                        </span>
                    @else
                        <span class="user-card-badge badge-user">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ __('Employee') }}
                        </span>
                    @endif
                    <div class="user-card-date">
                        {{ __('Since') }} {{ $user->created_at?->locale(app()->getLocale())->diffForHumans() }}
                    </div>
                    <div class="user-card-actions">
                        @unless(auth()->user()->role === 'supervisor' && $user->role === 'admin')
                        <button type="button" class="btn btn-outline"
                                onclick="openEditModal({{ $user->id }}, {{ Js::from($user->name) }}, '{{ $user->role }}', {{ Js::from($user->avatar ? Storage::url($user->avatar) : '') }})"
                                title="Modifier">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            {{ __('Edit') }}
                        </button>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('Delete this user?') }}');" style="flex:1;display:flex;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="flex:1;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                {{ __('Delete') }}
                            </button>
                        </form>
                        @endunless
                    </div>
                </div>
            @endforeach
        </div>
    @endif

{{-- ── Create User Modal ── --}}
<div class="edit-overlay" id="create-overlay" onclick="closeCreateModal(event)">
    <div class="edit-modal">
        <div class="edit-modal-header">
            <div class="edit-modal-avatar" style="background:var(--teal);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div>
                <div class="edit-modal-htitle">{{ __('Create a user') }}</div>
                <div class="edit-modal-hsub">{{ __('New employee or administrator account') }}</div>
            </div>
            <button class="edit-modal-close" type="button" onclick="closeCreateModal()" title="{{ __('Close') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="edit-modal-body">
                {{-- Avatar upload --}}
                <div class="edit-field">
                    <label>{{ __('Profile picture') }} <span style="color:#9bb0b0;font-weight:500;">({{ __('optional') }})</span></label>
                    <div class="modal-avatar-drop" onclick="document.getElementById('create-avatar-input').click()">
                        <input type="file" id="create-avatar-input" name="avatar" accept="image/*"
                               onchange="previewModalAvatar(this,'create-av-preview')">
                        <div class="modal-av-preview" id="create-av-preview" style="background:var(--teal);">
                            <span id="create-av-initials">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </span>
                        </div>
                        <div>
                            <div class="modal-av-info-title">{{ __('Choose a photo') }}</div>
                            <div class="modal-av-info-sub">JPG, PNG · max 2 Mo</div>
                        </div>
                    </div>
                </div>
                <div class="edit-field">
                    <label for="create-name">{{ __('Username') }}</label>
                    <input type="text" id="create-name" name="name"
                           value="{{ old('name') }}" required placeholder="Ex: jean.dupont">
                </div>
                <div class="edit-field">
                    <label for="create-role">{{ __('Role') }}</label>
                    <select id="create-role" name="role" required>
                        <option value="user" {{ old('role','user') === 'user' ? 'selected' : '' }}>{{ __('Employee') }}</option>
                        <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>{{ __('Supervisor') }}</option>
                        @if(auth()->user()->role === 'admin')
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('Administrator') }}</option>
                        @endif
                    </select>
                </div>
                <div class="edit-field">
                    <label for="create-password">{{ __('Password') }}</label>
                    <input type="password" id="create-password" name="password"
                           required placeholder="{{ __('Min. 8 characters') }}">
                    <div class="edit-field-hint">{{ __('Min. 8 characters') }}</div>
                </div>
            </div>
            <div class="edit-modal-footer">
                <button type="button" class="edit-btn-cancel" onclick="closeCreateModal()">{{ __('Cancel') }}</button>
                <button type="submit" class="edit-btn-save">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    {{ __('Create account') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit User Modal ── --}}
<div class="edit-overlay" id="edit-overlay" onclick="closeEditModal(event)">
    <div class="edit-modal">
        <div class="edit-modal-header">
            <div class="edit-modal-avatar" id="modal-avatar"></div>
            <div>
                <div class="edit-modal-htitle">{{ __('Edit user') }}</div>
                <div class="edit-modal-hsub" id="modal-subtitle">{{ __('Edit account information') }}</div>
            </div>
            <button class="edit-modal-close" type="button" onclick="closeEditModal()" title="{{ __('Close') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form id="edit-form" method="POST" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="edit-modal-body">
                {{-- Avatar upload --}}
                <div class="edit-field">
                    <label>{{ __('Profile picture') }} <span style="color:#9bb0b0;font-weight:500;">({{ __('optional') }})</span></label>
                    <div class="modal-avatar-drop" onclick="document.getElementById('edit-avatar-input').click()">
                        <input type="file" id="edit-avatar-input" name="avatar" accept="image/*"
                               onchange="previewModalAvatar(this,'edit-av-preview')">
                        <div class="modal-av-preview" id="edit-av-preview" style="background:#6366f1;">
                            <span id="edit-av-initials"></span>
                        </div>
                        <div>
                            <div class="modal-av-info-title">{{ __('Change photo') }}</div>
                            <div class="modal-av-info-sub">JPG, PNG · max 2 Mo</div>
                        </div>
                    </div>
                </div>
                <div class="edit-field">
                    <label for="modal-name">{{ __('Username') }}</label>
                    <input type="text" id="modal-name" name="name" required placeholder="{{ __('Display name') }}">
                </div>
                <div class="edit-field">
                    <label for="modal-role">{{ __('Role') }}</label>
                    <select id="modal-role" name="role" required>
                        <option value="user">{{ __('Employee') }}</option>
                        <option value="supervisor">{{ __('Supervisor') }}</option>
                        @if(auth()->user()->role === 'admin')
                        <option value="admin">{{ __('Administrator') }}</option>
                        @endif
                    </select>
                </div>
                <div class="edit-field">
                    <label for="modal-password">{{ __('New password') }} <span style="color:#9bb0b0;font-weight:500;">({{ __('optional') }})</span></label>
                    <input type="password" id="modal-password" name="password" placeholder="{{ __('Leave blank to keep unchanged') }}">
                    <div class="edit-field-hint">{{ __('Min. 8 characters') }}</div>
                </div>
            </div>
            <div class="edit-modal-footer">
                <button type="button" class="edit-btn-cancel" onclick="closeEditModal()">{{ __('Cancel') }}</button>
                <button type="submit" class="edit-btn-save">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewModalAvatar(input, previewId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById(previewId).innerHTML = '<img src="' + e.target.result + '" alt="avatar">';
    };
    reader.readAsDataURL(input.files[0]);
}

function openCreateModal() {
    const prev = document.getElementById('create-av-preview');
    prev.innerHTML = '<span><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg></span>';
    document.getElementById('create-avatar-input').value = '';
    document.getElementById('create-overlay').classList.add('open');
    document.getElementById('create-name').focus();
}

function closeCreateModal(e) {
    if (e && e.target !== document.getElementById('create-overlay')) return;
    document.getElementById('create-overlay').classList.remove('open');
}

const EDIT_ROUTE_BASE = '{{ url('admin/users') }}';
const COLORS = ['#0c7070','#6366f1','#d4a017','#e11d48','#0891b2','#16a34a'];

function openEditModal(id, name, role, avatarUrl) {
    const initials = name.split(' ').map(w => (w[0] || '').toUpperCase()).slice(0,2).join('');
    const bg       = COLORS[id % COLORS.length];

    document.getElementById('modal-avatar').textContent = initials;
    document.getElementById('modal-avatar').style.background = bg;
    document.getElementById('modal-subtitle').textContent = 'Compte #' + id + ' — ' + name;

    const prev = document.getElementById('edit-av-preview');
    prev.style.background = bg;
    prev.innerHTML = avatarUrl
        ? '<img src="' + avatarUrl + '" alt="avatar">'
        : '<span style="font-size:1.1rem;font-weight:800;color:#fff;">' + initials + '</span>';
    document.getElementById('edit-avatar-input').value = '';

    document.getElementById('modal-name').value = name;
    document.getElementById('modal-role').value = role;
    document.getElementById('modal-password').value = '';
    document.getElementById('edit-form').action = EDIT_ROUTE_BASE + '/' + id;
    document.getElementById('edit-overlay').classList.add('open');
    document.getElementById('modal-name').focus();
}

function closeEditModal(e) {
    if (e && e.target !== document.getElementById('edit-overlay')) return;
    document.getElementById('edit-overlay').classList.remove('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('edit-overlay').classList.remove('open');
        document.getElementById('create-overlay').classList.remove('open');
    }
});
</script>
@endpush

@endsection
