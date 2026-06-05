@extends('layouts.app')
@push('title')
    {{ __('Sign In') }} -
@endpush

@push('style')
<style>
    /* ===== LOGIN PAGE OVERRIDES ===== */
    #headless-wrapper {
        min-height: 100vh;
        background: var(--dt-bg-base, #080c17);
        position: relative;
        overflow: hidden;
    }

    #headless-wrapper::before {
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

    #headless-wrapper::after {
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

    .login-split {
        position: relative;
        z-index: 1;
        min-height: 100vh;
        display: flex;
    }

    /* ===== LEFT PANEL — form ===== */
    .login-form-panel {
        width: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 72px;
        position: relative;
    }

    .login-inner {
        width: 100%;
        max-width: 400px;
    }

    .login-logo {
        margin-bottom: 36px;
    }

    .login-logo img {
        max-height: 40px;
        filter: brightness(0) invert(1) drop-shadow(0 0 16px rgba(99,102,241,0.4));
    }

    .login-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(99,102,241,0.12);
        border: 1px solid rgba(99,102,241,0.3);
        border-radius: 100px;
        padding: 4px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #a5b4fc;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    .login-title {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #f1f5f9;
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin-bottom: 8px;
    }

    .login-subtitle {
        font-size: 0.88rem;
        color: #64748b;
        margin-bottom: 36px;
    }

    .login-subtitle a {
        color: #818cf8 !important;
        font-weight: 500;
        text-decoration: none;
    }

    .login-subtitle a:hover { text-decoration: underline; }

    /* Form fields */
    .login-field-wrap { margin-bottom: 20px; }

    .login-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: #94a3b8;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .login-input {
        width: 100%;
        height: 48px;
        background: rgba(10, 16, 34, 0.85);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        padding: 0 18px;
        color: #e2e8f0;
        font-size: 0.88rem;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
        outline: none;
    }

    .login-input:focus {
        border-color: rgba(99,102,241,0.5);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
    }

    .login-input::placeholder { color: #3e4f6a; }

    .login-input-wrap {
        position: relative;
    }

    .login-input-wrap .login-input {
        padding-right: 48px;
    }

    .pass-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        cursor: pointer;
        transition: color 0.15s ease;
    }

    .pass-icon:hover { color: #94a3b8; }

    /* Remember / Forgot row */
    .login-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    .login-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #64748b;
        cursor: pointer;
    }

    .login-remember input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: #6366f1;
        cursor: pointer;
    }

    .login-forgot {
        font-size: 0.82rem;
        color: #6366f1 !important;
        font-weight: 500;
        text-decoration: none;
    }

    .login-forgot:hover { text-decoration: underline; }

    /* Submit button */
    .login-btn {
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
        box-shadow: 0 6px 24px rgba(99,102,241,0.35);
        transition: all 0.18s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 28px;
    }

    .login-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 32px rgba(99,102,241,0.5);
    }

    /* Demo credentials */
    .login-demo-table {
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.06);
    }

    .login-demo-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .login-demo-table td {
        padding: 9px 14px;
        font-size: 0.78rem;
        color: #64748b;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .login-demo-table td:hover { background: rgba(99,102,241,0.08); color: #a5b4fc; }
    .login-demo-table td b { color: #94a3b8; }

    /* ===== RIGHT PANEL — illustration ===== */
    .login-art-panel {
        width: 50%;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 56px;
        background: linear-gradient(145deg, rgba(13,22,48,0.9) 0%, rgba(11,19,42,0.95) 100%);
        border-left: 1px solid rgba(255,255,255,0.06);
        overflow: hidden;
    }

    .login-art-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 55% at 30% 20%, rgba(59,130,246,0.16) 0%, transparent 60%),
            radial-gradient(ellipse 60% 45% at 70% 80%, rgba(99,102,241,0.13) 0%, transparent 55%);
        pointer-events: none;
    }

    /* Grid on art panel */
    .login-art-panel::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(rgba(255,255,255,0.025) 1px, transparent 1px);
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

    /* Stats row on art panel */
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

    /* Recaptcha */
    .g-recaptcha { margin-bottom: 20px; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 991.98px) {
        .login-art-panel { display: none; }
        .login-form-panel { width: 100%; padding: 40px 28px; }
        .login-inner { max-width: 100%; }
    }

    @media (max-width: 575.98px) {
        .login-form-panel { padding: 32px 20px; }
        .login-title { font-size: 1.7rem; }
    }
</style>
@endpush

@section('content')
<div id="headless-wrapper">
    <div class="login-split">

        {{-- LEFT: Form --}}
        <div class="login-form-panel">
            <div class="login-inner">

                <div class="login-logo">
                    <a href="/">
                        <img src="{{ getSettingImage('app_logo') }}" alt="{{ getOption('app_name') }}">
                    </a>
                </div>

                <div class="login-badge">
                    <i class="ri-shield-keyhole-line"></i>
                    {{ __('Secure Login') }}
                </div>

                <h1 class="login-title">{{ __('Sign in') }}</h1>

                @if (isAddonInstalled('PROTYSAAS') > 1)
                    <p class="login-subtitle">
                        {{ __('New owner?') }}
                        <a href="{{ route('owner.register.form') }}">{{ __('Create an account') }}</a>
                    </p>
                @else
                    <p class="login-subtitle">{{ __('Welcome back. Enter your credentials to continue.') }}</p>
                @endif

                <form action="{{ route('login') }}" method="post">
                    @csrf

                    {{-- Email --}}
                    <div class="login-field-wrap">
                        <label class="login-label" for="login-email">{{ __('Email address') }}</label>
                        <input type="text"
                               id="login-email"
                               name="email"
                               class="login-input email"
                               placeholder="you@company.com"
                               autocomplete="email">
                    </div>

                    {{-- Password --}}
                    <div class="login-field-wrap">
                        <label class="login-label" for="login-password">{{ __('Password') }}</label>
                        <div class="login-input-wrap">
                            <input type="password"
                                   id="login-password"
                                   name="password"
                                   class="login-input password"
                                   placeholder="••••••••"
                                   autocomplete="current-password">
                            <span class="toggle cursor fas fa-eye pass-icon"></span>
                        </div>
                    </div>

                    {{-- reCAPTCHA --}}
                    @if (getOption('GOOGLE_RECAPTCHA_MAIL_STATUS', 0) == ACTIVE)
                        <div class="g-recaptcha" data-sitekey="{{ getOption('GOOGLE_RECAPTCHA_KEY') }}"></div>
                        @if ($errors->has('g-recaptcha-response'))
                            <span class="text-danger" style="font-size:0.78rem;">{{ $errors->first('g-recaptcha-response') }}</span>
                        @endif
                    @endif

                    {{-- Remember / Forgot --}}
                    <div class="login-meta">
                        <label class="login-remember">
                            <input type="checkbox" name="remember" value="1" id="rememberMe">
                            {{ __('Remember me') }}
                        </label>
                        <a href="{{ route('password.request') }}" class="login-forgot">
                            {{ __('Forgot password?') }}
                        </a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="login-btn">
                        <i class="ri-login-circle-line"></i>
                        {{ __('Sign In') }}
                    </button>

                    {{-- Demo credentials --}}
                    @if (env('LOGIN_HELP') == 'active')
                        <div class="login-demo-table">
                            <table>
                                <tbody>
                                    <tr>
                                        <td colspan="2" id="adminCredentialShow">
                                            <b>Admin:</b> admin@gmail.com | 123456
                                            <span class="badge bg-danger ms-1" style="font-size:0.65rem;">
                                                <a href="{{ LINK_SAAS_ADDON }}" target="_blank" style="color:white">{{ __('SAAS Addon') }}</a>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" id="ownerCredentialShow">
                                            <b>Owner:</b> owner@gmail.com | 123456
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" id="tenantCredentialShow">
                                            <b>Tenant:</b> tenant@gmail.com | 123456
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" id="maintainerCredentialShow">
                                            <b>Maintainer:</b> maintainer@gmail.com | 123456
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                </form>
            </div>
        </div>

        {{-- RIGHT: Art panel --}}
        <div class="login-art-panel">
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

@push('script')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    "use strict";
    $('#adminCredentialShow').on('click', function() {
        $('.email').val('admin@gmail.com');
        $('.password').val('123456');
    });
    $('#ownerCredentialShow').on('click', function() {
        $('.email').val('owner@gmail.com');
        $('.password').val('123456');
    });
    $('#tenantCredentialShow').on('click', function() {
        $('.email').val('tenant@gmail.com');
        $('.password').val('123456');
    });
    $('#maintainerCredentialShow').on('click', function() {
        $('.email').val('maintainer@gmail.com');
        $('.password').val('123456');
    });
</script>
@endpush
