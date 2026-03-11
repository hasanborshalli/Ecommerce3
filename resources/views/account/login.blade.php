@extends('layouts.app')

@section('title', 'Sign In — ' . $siteName)

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <a href="{{ route('home') }}">{{ $siteName ?? 'Luma' }}<span style="color:var(--amber)">.</span></a>
        </div>

        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Sign in to your account to continue</p>

        @include('partials.flash')

        <form action="{{ route('account.login.post') }}" method="POST"
              style="display:flex;flex-direction:column;gap:var(--sp-4)">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       class="form-control{{ $errors->has('email') ? ' error' : '' }}"
                       value="{{ old('email') }}"
                       placeholder="you@example.com"
                       autocomplete="email" required>
                @error('email') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">
                    Password
                    <a href="{{ route('account.forgot') }}" class="auth-footer-link"
                       style="float:right;font-weight:var(--weight-normal)">Forgot?</a>
                </label>
                <input type="password" id="password" name="password"
                       class="form-control{{ $errors->has('password') ? ' error' : '' }}"
                       placeholder="Your password"
                       autocomplete="current-password" required>
                @error('password') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex;align-items:center;gap:var(--sp-2)">
                <input type="checkbox" id="remember" name="remember" value="1"
                       style="width:16px;height:16px;accent-color:var(--amber)">
                <label for="remember" style="font-size:var(--text-sm);color:var(--text-secondary);cursor:pointer">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                Sign In
            </button>
        </form>

        <div class="auth-divider"><span>or</span></div>

        <a href="{{ route('checkout') }}" class="btn btn-outline btn-full">
            Continue as Guest
        </a>

        <p style="text-align:center;margin-top:var(--sp-5);font-size:var(--text-sm);color:var(--text-muted)">
            Don't have an account?
            <a href="{{ route('account.register') }}" class="auth-footer-link">Create one</a>
        </p>

    </div>
</div>
@endsection
