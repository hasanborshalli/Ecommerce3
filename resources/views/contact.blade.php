@extends('layouts.app')

@section('title', 'Contact — ' . $siteName)
@section('meta_description', 'Get in touch with ' . ($siteName ?? 'Luma') . '. We respond within 24 hours.')

@section('content')
<div class="container">
<div class="contact-page-wrap">

    <nav class="breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span class="breadcrumb-sep">›</span>
        <span class="breadcrumb-current">Contact</span>
    </nav>

    <div class="contact-grid">

        {{-- Left: form --}}
        <div>
            <div style="margin-bottom:var(--sp-8)">
                <div class="label-overline" style="margin-bottom:var(--sp-2)">Get in touch</div>
                <h1 class="display-md" style="margin-bottom:var(--sp-3)">We'd love to hear from you.</h1>
                <p style="color:var(--text-secondary);font-size:var(--text-base)">
                    Questions, feedback, or just want to say hello — we respond within 24 hours.
                </p>
            </div>

            @include('partials.flash')

            <form action="{{ route('contact.submit') }}" method="POST"
                  style="display:flex;flex-direction:column;gap:var(--sp-5)">
                @csrf

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name <span class="req">*</span></label>
                        <input type="text" id="name" name="name"
                               class="form-control{{ $errors->has('name') ? ' error' : '' }}"
                               value="{{ old('name', session('customer_name') ?? '') }}"
                               placeholder="Your name" required>
                        @error('name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone"
                               class="form-control"
                               value="{{ old('phone') }}"
                               placeholder="+1 555 000 0000">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address <span class="req">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-control{{ $errors->has('email') ? ' error' : '' }}"
                           value="{{ old('email', session('customer_email') ?? '') }}"
                           placeholder="you@example.com" required>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">Subject</label>
                    <input type="text" id="subject" name="subject"
                           class="form-control"
                           value="{{ old('subject') }}"
                           placeholder="What's this about?">
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">Message <span class="req">*</span></label>
                    <textarea id="message" name="message" rows="5"
                              class="form-control{{ $errors->has('message') ? ' error' : '' }}"
                              placeholder="Tell us how we can help…" required>{{ old('message') }}</textarea>
                    @error('message') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Send Message
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: info panel --}}
        <div>
            <div class="contact-info-panel">

                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.93 19.79 19.79 0 01.14 1.35 2 2 0 012.11 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z"/></svg>
                    </div>
                    <div>
                        <div class="contact-info-label">Phone</div>
                        @if(!empty($settings['site_phone']))
                        <a href="tel:{{ $settings['site_phone'] }}" class="contact-info-value">{{ $settings['site_phone'] }}</a>
                        @else
                        <span class="contact-info-value">—</span>
                        @endif
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <div class="contact-info-label">Email</div>
                        @if(!empty($settings['site_email']))
                        <a href="mailto:{{ $settings['site_email'] }}" class="contact-info-value">{{ $settings['site_email'] }}</a>
                        @else
                        <span class="contact-info-value">—</span>
                        @endif
                    </div>
                </div>

                @if(!empty($settings['site_address']))
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <div class="contact-info-label">Address</div>
                        <span class="contact-info-value">{{ $settings['site_address'] }}</span>
                    </div>
                </div>
                @endif

                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div class="contact-info-label">Response time</div>
                        <span class="contact-info-value">Within 24 hours</span>
                    </div>
                </div>

            </div>
        </div>

    </div>{{-- /contact-grid --}}

</div>
</div>
@endsection
