@extends('layouts.app')

@section('content')
    <section class="panel" style="max-width: 560px; margin: 0 auto;">
        <h1 class="title">Register</h1>
        <p class="subtitle">Create an account. The first registered account becomes admin automatically; next accounts are normal users.</p>

        <form method="POST" action="{{ route('register.store') }}" class="grid" style="margin-top: 1rem;">
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
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Create account</button>
        </form>

        <p class="subtitle" style="margin-top: 1rem;">
            Already registered? <a href="{{ route('login') }}">Login</a>.
        </p>
    </section>
@endsection
