@extends('layouts.app')

@section('title', 'Create Account — ' . $siteName)

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <a href="{{ route('home') }}">{{ $siteName ?? 'Luma' }}<span style="color:var(--amber)">.</span></a>
        </div>

        <h1 class="auth-title">Create an account</h1>
        <p class="auth-subtitle">Join {{ $siteName }} for order tracking and faster checkout</p>

        @include('partials.flash')

        <form action="{{ route('account.register.post') }}" method="POST"
              style="display:flex;flex-direction:column;gap:var(--sp-4)">
            @csrf

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                           class="form-control{{ $errors->has('first_name') ? ' error' : '' }}"
                           value="{{ old('first_name') }}"
                           placeholder="First name"
                           autocomplete="given-name" required>
                    @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name"
                           class="form-control{{ $errors->has('last_name') ? ' error' : '' }}"
                           value="{{ old('last_name') }}"
                           placeholder="Last name"
                           autocomplete="family-name" required>
                    @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address <span class="req">*</span></label>
                <input type="email" id="email" name="email"
                       class="form-control{{ $errors->has('email') ? ' error' : '' }}"
                       value="{{ old('email') }}"
                       placeholder="you@example.com"
                       autocomplete="email" required>
                @error('email') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone <span style="color:var(--text-muted);font-weight:normal">(optional)</span></label>
                <input type="tel" id="phone" name="phone"
                       class="form-control"
                       value="{{ old('phone') }}"
                       placeholder="+1 555 000 0000"
                       autocomplete="tel">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password <span class="req">*</span></label>
                <input type="password" id="password" name="password"
                       class="form-control{{ $errors->has('password') ? ' error' : '' }}"
                       placeholder="At least 8 characters"
                       autocomplete="new-password" required>
                @error('password') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password <span class="req">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control"
                       placeholder="Repeat your password"
                       autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                Create Account
            </button>

            <p style="font-size:var(--text-xs);color:var(--text-muted);text-align:center;line-height:var(--leading-relaxed)">
                By creating an account you agree to our terms of service and privacy policy.
            </p>
        </form>

        <p style="text-align:center;margin-top:var(--sp-5);font-size:var(--text-sm);color:var(--text-muted)">
            Already have an account?
            <a href="{{ route('account.login') }}" class="auth-footer-link">Sign in</a>
        </p>

    </div>
</div>
@endsection
