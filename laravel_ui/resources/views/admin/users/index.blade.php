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
    .badge-admin  { background: #fef3c7; color: #92400e; }
    .badge-user   { background: #e6f4f4; color: #0c7070; }
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
</style>
@endpush

@section('content')

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="page-header-title">Gestion des utilisateurs</div>
            <div class="page-header-sub">Créer, modifier et supprimer les comptes employés et administrateurs.</div>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvel utilisateur
        </a>
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
                <div class="user-stat-label">Total utilisateurs</div>
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
                <div class="user-stat-label">Administrateurs</div>
            </div>
        </div>
        <div class="user-stat">
            <div class="user-stat-icon" style="background:#f0f0ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="user-stat-val">{{ $users->where('role','user')->count() }}</div>
                <div class="user-stat-label">Employés</div>
            </div>
        </div>
    </div>

    {{-- User cards --}}
    @if($users->isEmpty())
        <div class="empty-state">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#6b8080" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <p>Aucun utilisateur trouvé.</p>
        </div>
    @else
        <div class="user-grid">
            @foreach($users as $user)
                @php
                    $isAdmin = $user->role === 'admin';
                    $colors  = ['#0c7070','#6366f1','#d4a017','#e11d48','#0891b2','#16a34a'];
                    $bg      = $colors[$user->id % count($colors)];
                    $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                @endphp
                <div class="user-card">
                    <div class="user-card-stripe" style="background: {{ $isAdmin ? 'linear-gradient(90deg,#d4a017,#f5cb5c)' : 'linear-gradient(90deg,#0c7070,#14a8a8)' }};"></div>
                    <div class="user-card-avatar" style="background: {{ $bg }};">{{ $initials }}</div>
                    <div class="user-card-name">{{ $user->name }}</div>
                    <div class="user-card-id">#{{ $user->id }}</div>
                    <span class="user-card-badge {{ $isAdmin ? 'badge-admin' : 'badge-user' }}">
                        @if($isAdmin)
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Admin
                        @else
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Employé
                        @endif
                    </span>
                    <div class="user-card-date">
                        Depuis {{ $user->created_at?->locale('fr')->diffForHumans() }}
                    </div>
                    <div class="user-card-actions">
                        <a class="btn btn-outline" href="{{ route('admin.users.edit', $user) }}">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Supprimer cet utilisateur ?');" style="flex:1;display:flex;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="flex:1;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
