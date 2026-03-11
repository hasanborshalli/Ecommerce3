@extends('layouts.app')

@section('title', 'Profile — ' . $siteName)

@section('content')
<div class="container">
<div class="account-layout">

    @include('account.partials.nav', ['active' => 'profile'])

    <div class="account-main">

        {{-- Personal details --}}
        <div class="account-section" style="margin-bottom:var(--sp-5)">
            <h1 class="account-section-title" style="margin-bottom:var(--sp-6)">Personal Details</h1>

            @include('partials.flash')

            <form action="{{ route('account.profile.update') }}" method="POST"
                  style="display:flex;flex-direction:column;gap:var(--sp-4)">
                @csrf

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name <span class="req">*</span></label>
                        <input type="text" id="first_name" name="first_name"
                               class="form-control{{ $errors->has('first_name') ? ' error' : '' }}"
                               value="{{ old('first_name', $customer->first_name) }}"
                               required>
                        @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name <span class="req">*</span></label>
                        <input type="text" id="last_name" name="last_name"
                               class="form-control{{ $errors->has('last_name') ? ' error' : '' }}"
                               value="{{ old('last_name', $customer->last_name) }}"
                               required>
                        @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address <span class="req">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-control{{ $errors->has('email') ? ' error' : '' }}"
                           value="{{ old('email', $customer->email) }}"
                           required>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone"
                           class="form-control"
                           value="{{ old('phone', $customer->phone) }}"
                           placeholder="+1 555 000 0000">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" name="action" value="profile">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change password --}}
        <div class="account-section">
            <h2 style="font-size:var(--text-lg);font-weight:var(--weight-semibold);margin-bottom:var(--sp-6)">Change Password</h2>

            <form action="{{ route('account.profile.update') }}" method="POST"
                  style="display:flex;flex-direction:column;gap:var(--sp-4)">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password <span class="req">*</span></label>
                    <input type="password" id="current_password" name="current_password"
                           class="form-control{{ $errors->has('current_password') ? ' error' : '' }}"
                           placeholder="Your current password">
                    @error('current_password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" for="new_password">New Password <span class="req">*</span></label>
                        <input type="password" id="new_password" name="new_password"
                               class="form-control{{ $errors->has('new_password') ? ' error' : '' }}"
                               placeholder="At least 8 characters">
                        @error('new_password') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="new_password_confirmation">Confirm New Password <span class="req">*</span></label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               class="form-control"
                               placeholder="Repeat new password">
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn btn-outline" name="action" value="password">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
</div>
@endsection
