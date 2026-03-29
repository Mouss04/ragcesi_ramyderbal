@extends('layouts.admin')

@section('content')
    <section class="panel" style="max-width: 700px;">
        <h1 class="title">Create User</h1>
        <p class="subtitle">Create a new account and assign the role.</p>

        <form method="POST" action="{{ route('admin.users.store') }}" class="grid" style="margin-top: 1rem;">
            @csrf
            <div>
                <label for="name">Username</label>
                <input id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div>
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user" @selected(old('role') === 'user')>Normal User</option>
                    <option value="supervisor" @selected(old('role') === 'supervisor')>Supervisor</option>
                    @if(auth()->user()->role === 'admin')
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    @endif
                </select>
            </div>
            <div style="display: flex; gap: 0.6rem;">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </section>
@endsection
