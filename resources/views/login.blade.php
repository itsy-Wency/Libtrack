{{-- Public sign-in form; authentication and validation are handled by AuthController. --}}
@extends('layouts.app')

@section('title', 'Sign in | LibTrack')

@section('content')
    <section class="card auth-card">
        <p class="eyebrow">Welcome to LibTrack</p>
        <h1>Sign in</h1>
        <p class="muted">Access your library workspace to browse titles and manage requests.</p>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">

            <div class="form-actions">
                <button type="submit"><svg class="icon"><use href="#icon-user" /></svg>Sign in</button>
            </div>
        </form>

        <p class="muted">Demo accounts are listed in README.md. Password: password</p>
    </section>
@endsection
