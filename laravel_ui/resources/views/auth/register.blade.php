@extends('layouts.app')

@section('content')
    <section class="panel" style="max-width: 560px; margin: 0 auto;">
        <h1 class="title">Register</h1>
        <p class="subtitle">Create your company account. You will be the administrator for your company.</p>

        <form method="POST" action="{{ route('register.store') }}" class="grid" style="margin-top: 1rem;">
            @csrf
            <div>
                <label for="company_name">Company name</label>
                <input id="company_name" name="company_name" value="{{ old('company_name') }}" required placeholder="Ex: Acme Corp">
                @error('company_name')<span style="color:red;font-size:.85rem;">{{ $message }}</span>@enderror
            </div>
            <div>
                <label for="name">Username</label>
                <input id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<span style="color:red;font-size:.85rem;">{{ $message }}</span>@enderror
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
