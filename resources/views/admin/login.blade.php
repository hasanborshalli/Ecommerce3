<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login · {{ $siteName ?? 'Luma' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<div class="admin-login-page">
    <div class="admin-login-card">

        {{-- Logo --}}
        <div class="admin-login-logo">
            <div class="admin-login-logo-mark">L</div>
        </div>

        <h1 class="admin-login-title">Welcome back</h1>
        <p class="admin-login-subtitle">Sign in to the {{ $siteName ?? 'Luma' }} admin panel</p>

        @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:var(--sp-4)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" style="display:flex;flex-direction:column;gap:var(--sp-4)">
            @csrf

            <div class="aform-group">
                <label class="aform-label" for="email">Email address</label>
                <input type="email" id="email" name="email"
                       class="aform-control{{ $errors->has('email') ? ' error' : '' }}"
                       value="{{ old('email') }}"
                       placeholder="admin@example.com"
                       autocomplete="email" required autofocus>
                @error('email')
                    <span class="aform-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="aform-group">
                <label class="aform-label" for="password">Password</label>
                <input type="password" id="password" name="password"
                       class="aform-control{{ $errors->has('password') ? ' error' : '' }}"
                       placeholder="••••••••"
                       autocomplete="current-password" required>
                @error('password')
                    <span class="aform-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="abtn abtn-amber abtn-lg abtn-full" style="margin-top:var(--sp-2)">
                Sign In
            </button>
        </form>

        <div style="margin-top:var(--sp-6);text-align:center">
            <a href="{{ route('home') }}"
               style="font-size:var(--text-sm);color:var(--admin-muted);display:inline-flex;align-items:center;gap:var(--sp-1)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Back to store
            </a>
        </div>

    </div>
</div>

</body>
</html>
