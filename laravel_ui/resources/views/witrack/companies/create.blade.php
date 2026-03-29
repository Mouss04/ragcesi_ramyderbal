@extends('layouts.witrack')

@section('content')
<div style="max-width:580px;">
    <div style="margin-bottom:1.2rem;">
        <a href="{{ route('witrack.dashboard') }}" style="font-size:.85rem;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Retour aux entreprises
        </a>
    </div>

    <h1 class="page-title">Nouvelle entreprise</h1>

    <div class="panel">
        <p style="color:var(--muted);font-size:.88rem;margin-bottom:1.4rem;">
            Renseignez le nom de l'entreprise ainsi que les identifiants de son administrateur.
            L'administrateur aura ensuite accès à son tableau de bord pour gérer ses utilisateurs et documents.
        </p>

        <form method="POST" action="{{ route('witrack.companies.store') }}" class="grid">
            @csrf

            {{-- Company --}}
            <div>
                <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--teal);margin-bottom:.8rem;">
                    Entreprise
                </div>
                <div>
                    <label for="company_name">Nom de l'entreprise <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="company_name" name="company_name"
                           value="{{ old('company_name') }}"
                           placeholder="Ex : Acme Corp"
                           required>
                    @error('company_name')
                        <span style="color:#dc2626;font-size:.82rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Separator --}}
            <hr style="border:none;border-top:1px solid var(--line);">

            {{-- Admin account --}}
            <div>
                <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--teal);margin-bottom:.8rem;">
                    Compte administrateur
                </div>
                <div class="grid">
                    <div>
                        <label for="admin_name">Nom d'utilisateur <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="admin_name" name="admin_name"
                               value="{{ old('admin_name') }}"
                               placeholder="Ex : admin_acme"
                               required>
                        @error('admin_name')
                            <span style="color:#dc2626;font-size:.82rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="admin_password">Mot de passe <span style="color:#dc2626;">*</span></label>
                        <input type="password" id="admin_password" name="admin_password"
                               placeholder="Min. 8 caractères"
                               required>
                        @error('admin_password')
                            <span style="color:#dc2626;font-size:.82rem;margin-top:.25rem;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="admin_password_confirmation">Confirmer le mot de passe <span style="color:#dc2626;">*</span></label>
                        <input type="password" id="admin_password_confirmation" name="admin_password_confirmation"
                               placeholder="Répétez le mot de passe"
                               required>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;margin-top:.4rem;">
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Créer l'entreprise
                </button>
                <a href="{{ route('witrack.dashboard') }}" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
