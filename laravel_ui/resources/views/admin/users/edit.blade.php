@extends('layouts.admin')

@section('content')
    <section class="panel" style="max-width: 700px;">
        <h1 class="title">Edit User</h1>
        <p class="subtitle">Update username, role, and optionally password.</p>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid" style="margin-top: 1rem;">
            @csrf
            @method('PUT')
            <div>
                <label for="name">Username</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div>
                <label for="password">New Password (optional)</label>
                <input id="password" name="password" type="password">
            </div>
            <div>
                <label for="role">Role</label>
                @if($user->role === 'admin' || $user->id === auth()->id())
                    <input type="text" value="{{ ucfirst($user->role === 'user' ? 'Employé' : ucfirst($user->role)) }}" disabled style="background:#f3f4f6;color:#6b7280;cursor:not-allowed;">
                    <input type="hidden" name="role" value="{{ $user->role }}">
                @else
                <select id="role" name="role" required>
                    <option value="user" @selected(old('role', $user->role) === 'user')>Normal User</option>
                    <option value="supervisor" @selected(old('role', $user->role) === 'supervisor')>Supervisor</option>
                </select>
                @endif
            </div>
            <div style="display: flex; gap: 0.6rem;">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </section>
@endsection
