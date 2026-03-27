@extends('layouts.app')

@section('content')
    <section class="panel">
        <h1 class="title">Admin Dashboard</h1>
        <p class="subtitle">Manage users, upload PDFs, and trigger RAG indexing.</p>

        <div class="grid cols-3" style="margin-top: 1rem;">
            <article class="card">
                <p class="subtitle" style="margin: 0;">Total Users</p>
                <p class="metric">{{ $usersCount }}</p>
            </article>
            <article class="card">
                <p class="subtitle" style="margin: 0;">Uploaded Documents</p>
                <p class="metric">{{ $documentsCount }}</p>
            </article>
            <article class="card">
                <p class="subtitle" style="margin: 0;">Access</p>
                <p class="metric" style="font-size: 1.2rem; margin-top: 0.5rem;">Admin</p>
            </article>
        </div>

        <div class="grid cols-2" style="margin-top: 1rem;">
            <a class="btn btn-primary" href="{{ route('admin.users.index') }}">Manage Users</a>
            <a class="btn btn-muted" href="{{ route('admin.documents.index') }}">Manage Documents</a>
        </div>
    </section>
@endsection
