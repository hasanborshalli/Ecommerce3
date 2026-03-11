@extends('layouts.app')

@section('title', 'Set New Password — ' . $siteName)

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <a href="{{ route('home') }}">{{ $siteName ?? 'Luma' }}<span style="color:var(--amber)">.</span></a>
        </div>

        <h1 class="auth-title">Set a new password</h1>
        <p class="auth-subtitle">Choose a strong password for your account</p>

        @include('partials.flash')

        <form action="{{ route('account.reset.post') }}" method="POST"
              style="display:flex;flex-direction:column;gap:var(--sp-4)">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       class="form-control{{ $errors->has('email') ? ' error' : '' }}"
                       value="{{ old('email', $email ?? '') }}"
                       placeholder="you@example.com"
                       autocomplete="email" required>
                @error('email') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input type="password" id="password" name="password"
                       class="form-control{{ $errors->has('password') ? ' error' : '' }}"
                       placeholder="At least 8 characters"
                       autocomplete="new-password" required>
                @error('password') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control"
                       placeholder="Repeat your password"
                       autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                Reset Password
            </button>
        </form>

    </div>
</div>
@endsection
