@extends('layouts.app')
@push('title')
    {{ __('Reset Password') }} -
@endpush

@push('style')
<style>
    /* ===== RESET PASSWORD PAGE ===== */
    #headless-wrapper {
        min-height: 100vh;
        background: var(--t-bg-base, #f0f4f8);
        position: relative;
        overflow: hidden;
    }

    [data-bs-theme="dark"] #headless-wrapper {
        background: var(--dt-bg-base, #080c17);
    }

    [data-bs-theme="dark"] #headless-wrapper::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(ellipse 80% 60% at 15% 10%,  rgba(59,130,246,0.15) 0%, transparent 60%),
            radial-gradient(ellipse 60% 50% at 85% 85%,  rgba(99,102,241,0.12) 0%, transparent 55%),
            radial-gradient(ellipse 50% 40% at 55% 40%,  rgba(16,185,129,0.07) 0%, transparent 50%);
        pointer-events: none;
        z-index: 0;
    }

    [data-bs-theme="dark"] #headless-wrapper::after {
        content: '';
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.022) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.022) 1px, transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
        z-index: 0;
    }

    .auth-split {
        position: relative;
        z-index: 1;
        min-height: 100vh;
        display: flex;
    }

    /* ===== LEFT PANEL ===== */
    .auth-form-panel {
        width: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 72px;
        background: var(--t-bg-surface, #ffffff);
        border-right: 1px solid var(--t-border, #e2e8f0);
        position: relative;
    }

    [data-bs-theme="dark"] .auth-form-panel {
        background: transparent;
        border-right-color: rgba(255,255,255,0.06);
    }

    .auth-inner {
        width: 100%;
        max-width: 400px;
    }

    .auth-logo {
        margin-bottom: 36px;
    }

    .auth-logo img {
        max-height: 40px;
    }

    [data-bs-theme="dark"] .auth-logo img {
        filter: brightness(0) invert(1) drop-shadow(0 0 16px rgba(99,102,241,0.4));
    }

    .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(99,102,241,0.10);
        border: 1px solid rgba(99,102,241,0.25);
        border-radius: 100px;
        padding: 4px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #6366f1;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    [data-bs-theme="dark"] .auth-badge {
        color: #a5b4fc;
        background: rgba(99,102,241,0.12);
        border-color: rgba(99,102,241,0.3);
    }

    .auth-title {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: var(--t-text-primary, #0f172a);
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin-bottom: 8px;
    }

    [data-bs-theme="dark"] .auth-title {
        color: #f1f5f9;
    }

    .auth-subtitle {
        font-size: 0.88rem;
        color: var(--t-text-muted, #64748b);
        margin-bottom: 32px;
        line-height: 1.55;
    }

    /* Form fields */
    .auth-field-wrap { margin-bottom: 20px; }

    .auth-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--t-text-secondary, #475569);
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    [data-bs-theme="dark"] .auth-label {
        color: #94a3b8;
    }

    .auth-input {
        width: 100%;
        height: 48px;
        background: var(--t-bg-card, #f8fafc);
        border: 1px solid var(--t-border, #e2e8f0);
        border-radius: 10px;
        padding: 0 18px;
        color: var(--t-text-primary, #0f172a);
        font-size: 0.88rem;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
        outline: none;
    }

    [data-bs-theme="dark"] .auth-input {
        background: rgba(10, 16, 34, 0.85);
        border-color: rgba(255,255,255,0.08);
        color: #e2e8f0;
    }

    .auth-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
    }

    .auth-input::placeholder {
        color: var(--t-text-muted, #94a3b8);
    }

    [data-bs-theme="dark"] .auth-input::placeholder {
        color: #3e4f6a;
    }

    .auth-input-wrap {
        position: relative;
    }

    .auth-input-wrap .auth-input {
        padding-right: 48px;
    }

    .pass-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--t-text-muted, #94a3b8);
        cursor: pointer;
        transition: color 0.15s ease;
    }

    [data-bs-theme="dark"] .pass-icon {
        color: #475569;
    }

    .pass-icon:hover { color: #6366f1; }

    /* Submit button */
    .auth-btn {
        width: 100%;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        border: none;
        border-radius: 12px;
        color: #fff;
        font-size: 0.9rem;
        font-weight: 700;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 0.02em;
        cursor: pointer;
        box-shadow: 0 6px 24px rgba(99,102,241,0.3);
        transition: all 0.18s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 24px;
    }

    .auth-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 32px rgba(99,102,241,0.5);
    }

    .auth-back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.83rem;
        color: var(--t-text-muted, #64748b);
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .auth-back-link:hover {
        color: #6366f1;
    }

    /* ===== RIGHT PANEL — illustration ===== */
    .auth-art-panel {
        width: 50%;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 56px;
        background: linear-gradient(145deg, #0d1630 0%, #111f45 50%, #0d1a3a 100%);
        border-left: 1px solid rgba(255,255,255,0.06);
        overflow: hidden;
    }

    .auth-art-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 55% at 30% 20%, rgba(59,130,246,0.16) 0%, transparent 60%),
            radial-gradient(ellipse 60% 45% at 70% 80%, rgba(99,102,241,0.13) 0%, transparent 55%);
        pointer-events: none;
    }

    .auth-art-panel::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255,255,255,0.025) 1px, transparent 1px);
        background-size: 30px 30px;
        pointer-events: none;
    }

    .art-content {
        position: relative;
        z-index: 1;
        text-align: center;
        width: 100%;
        max-width: 440px;
    }

    .art-image-wrap {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(0,0,0,0.55);
        border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 36px;
        position: relative;
    }

    .art-image-wrap::before {
        content: '';
        position: absolute;
        top: 0; left: 10%; right: 10%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        z-index: 2;
    }

    .art-image-wrap img {
        width: 100%;
        display: block;
        filter: brightness(0.85) saturate(1.1);
    }

    .art-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.65rem;
        font-weight: 800;
        color: #f1f5f9;
        line-height: 1.25;
        margin-bottom: 12px;
        letter-spacing: -0.02em;
    }

    .art-subtitle {
        font-size: 0.9rem;
        color: rgba(255,255,255,0.45);
        line-height: 1.6;
        max-width: 320px;
        margin: 0 auto;
    }

    .art-stats {
        display: flex;
        justify-content: center;
        gap: 24px;
        margin-top: 36px;
    }

    .art-stat {
        text-align: center;
        padding: 14px 20px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 14px;
        backdrop-filter: blur(8px);
        min-width: 90px;
    }

    .art-stat-num {
        font-family: 'Poppins', sans-serif;
        font-size: 1.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: block;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .art-stat-lbl {
        font-size: 0.68rem;
        font-weight: 600;
        color: #475569;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 991.98px) {
        .auth-art-panel { display: none; }
        .auth-form-panel { width: 100%; padding: 40px 28px; }
        .auth-inner { max-width: 100%; }
    }

    @media (max-width: 575.98px) {
        .auth-form-panel { padding: 32px 20px; }
        .auth-title { font-size: 1.7rem; }
    }
</style>
@endpush

@section('content')
<div id="headless-wrapper">
    <div class="auth-split">

        {{-- LEFT: Form --}}
        <div class="auth-form-panel">
            <div class="auth-inner">

                <div class="auth-logo">
                    <a href="{{ route('frontend') }}">
                        <img src="{{ getSettingImage('app_logo') }}" alt="{{ getOption('app_name') }}">
                    </a>
                </div>

                <div class="auth-badge">
                    <i class="ri-refresh-line"></i>
                    {{ __('Set New Password') }}
                </div>

                <h1 class="auth-title">{{ __('Reset Password') }}</h1>
                <p class="auth-subtitle">{{ __('Enter your email and choose a new secure password below.') }}</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email --}}
                    <div class="auth-field-wrap">
                        <label class="auth-label" for="rp-email">{{ __('Email Address') }}</label>
                        <input type="email"
                               id="rp-email"
                               name="email"
                               class="auth-input form-control"
                               value="{{ $email ?? old('email') }}"
                               placeholder="you@company.com"
                               autocomplete="email">
                        @error('email')
                            <span class="text-danger" style="font-size:0.78rem;display:block;margin-top:5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="auth-field-wrap">
                        <label class="auth-label" for="rp-password">{{ __('New Password') }}</label>
                        <div class="auth-input-wrap">
                            <input type="password"
                                   id="rp-password"
                                   name="password"
                                   class="auth-input form-control password"
                                   placeholder="••••••••"
                                   autocomplete="new-password">
                            <span class="toggle cursor fas fa-eye pass-icon"></span>
                        </div>
                        @error('password')
                            <span class="text-danger" style="font-size:0.78rem;display:block;margin-top:5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="auth-field-wrap">
                        <label class="auth-label" for="rp-password-confirm">{{ __('Confirm Password') }}</label>
                        <div class="auth-input-wrap">
                            <input type="password"
                                   id="rp-password-confirm"
                                   name="password_confirmation"
                                   class="auth-input form-control password"
                                   placeholder="••••••••"
                                   autocomplete="new-password">
                            <span class="toggle cursor fas fa-eye pass-icon"></span>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="auth-btn">
                        <i class="ri-lock-2-line"></i>
                        {{ __('Reset Password') }}
                    </button>

                    <a href="{{ route('login') }}" class="auth-back-link">
                        <i class="ri-arrow-left-line"></i>
                        {{ __('← Back to Sign In') }}
                    </a>

                </form>
            </div>
        </div>

        {{-- RIGHT: Art panel --}}
        <div class="auth-art-panel">
            <div class="art-content">

                <div class="art-image-wrap">
                    <img src="{{ getSettingImage('sign_in_image') }}" alt="{{ getOption('app_name') }}">
                </div>

                <h2 class="art-title">{{ __(getOption('sign_in_text_title')) }}</h2>
                <p class="art-subtitle">{{ __(getOption('sign_in_text_subtitle')) }}</p>

                <div class="art-stats">
                    <div class="art-stat">
                        <span class="art-stat-num">99%</span>
                        <span class="art-stat-lbl">{{ __('Uptime') }}</span>
                    </div>
                    <div class="art-stat">
                        <span class="art-stat-num">256b</span>
                        <span class="art-stat-lbl">{{ __('Encrypted') }}</span>
                    </div>
                    <div class="art-stat">
                        <span class="art-stat-num">24/7</span>
                        <span class="art-stat-lbl">{{ __('Support') }}</span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
