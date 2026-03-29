@extends('layouts.app')

@section('content')
    <section class="panel" style="max-width: 520px; margin: 0 auto;">
        <h1 class="title">Login</h1>
        <p class="subtitle">Employees and admins can sign in using their username and password.</p>

        <form method="POST" action="{{ route('login.attempt') }}" class="grid" style="margin-top: 1rem;">
            @csrf
            <div>
                <label for="name">Username</label>
                <input id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
    </section>
@endsection
