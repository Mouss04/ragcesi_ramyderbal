@extends('layouts.witrack')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.4rem;flex-wrap:wrap;gap:0.75rem;">
    <h1 class="page-title" style="margin-bottom:0;">Entreprises</h1>
    <a href="{{ route('witrack.companies.create') }}" class="btn btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Nouvelle entreprise
    </a>
</div>

{{-- Stats strip --}}
<div class="grid cols-3" style="margin-bottom:1.4rem;">
    <div class="card">
        <div class="card-label">Total entreprises</div>
        <div class="card-metric">{{ $companies->count() }}</div>
    </div>
    <div class="card">
        <div class="card-label">Total utilisateurs</div>
        <div class="card-metric">{{ $companies->sum('users_count') }}</div>
    </div>
    <div class="card">
        <div class="card-label">Admins actifs</div>
        <div class="card-metric">{{ $companies->filter(fn($c) => $c->users->isNotEmpty())->count() }}</div>
    </div>
</div>

<div class="panel">
    @if($companies->isEmpty())
        <p style="color:var(--muted);text-align:center;padding:2rem 0;">Aucune entreprise créée. <a href="{{ route('witrack.companies.create') }}">Créer la première</a>.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Entreprise</th>
                    <th>Administrateur</th>
                    <th>Utilisateurs</th>
                    <th>Créée le</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                @php $admin = $company->users->first(); @endphp
                <tr>
                    <td>
                        <strong>{{ $company->name }}</strong>
                    </td>
                    <td>
                        @if($admin)
                            <span>{{ $admin->name }}</span>
                            <span class="pill pill-amber" style="margin-left:.4rem;">admin</span>
                        @else
                            <span style="color:var(--muted);font-style:italic;">— aucun admin —</span>
                        @endif
                    </td>
                    <td>
                        <span class="pill pill-teal">{{ $company->users_count }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $company->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:right;">
                        <form method="POST" action="{{ route('witrack.companies.destroy', $company) }}"
                              onsubmit="return confirm('Supprimer « {{ $company->name }} » et tous ses utilisateurs ?');"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding:0.35rem 0.7rem;font-size:.8rem;">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
