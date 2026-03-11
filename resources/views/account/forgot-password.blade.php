@extends('layouts.app')

@section('title', 'Reset Password — ' . $siteName)

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <a href="{{ route('home') }}">{{ $siteName ?? 'Luma' }}<span style="color:var(--amber)">.</span></a>
        </div>

        <h1 class="auth-title">Forgot your password?</h1>
        <p class="auth-subtitle">Enter your email and we'll send you a reset link</p>

        @include('partials.flash')

        @if(session('status'))
        <div class="alert alert-success" style="margin-bottom:var(--sp-5)">
            {{ session('status') }}
        </div>
        @endif

        <form action="{{ route('account.forgot.post') }}" method="POST"
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

            <button type="submit" class="btn btn-primary btn-full">
                Send Reset Link
            </button>
        </form>

        <p style="text-align:center;margin-top:var(--sp-5);font-size:var(--text-sm);color:var(--text-muted)">
            Remembered it?
            <a href="{{ route('account.login') }}" class="auth-footer-link">Back to sign in</a>
        </p>

    </div>
</div>
@endsection
