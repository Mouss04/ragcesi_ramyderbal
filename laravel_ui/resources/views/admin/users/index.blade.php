@extends('layouts.app')

@section('content')
    <section class="panel">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <div>
                <h1 class="title">Users</h1>
                <p class="subtitle">Create, edit and delete employee or admin accounts.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
        </div>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Created</th>
                <th style="width: 240px;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td><span class="pill">{{ $user->role }}</span></td>
                    <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a class="btn btn-muted" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No users found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
