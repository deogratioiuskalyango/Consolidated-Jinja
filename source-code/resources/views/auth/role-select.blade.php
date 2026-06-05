@extends('layouts.app')

@push('title')
    {{ __('Select Dashboard') }} -
@endpush

@push('style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* ===== RESET & BASE ===== */
    #headless-wrapper {
        min-height: 100vh;
        background: #0b0f1a;
        position: relative;
        overflow: hidden;
    }

    /* Animated mesh background */
    #headless-wrapper::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(ellipse 80% 60% at 20% 10%, rgba(59, 130, 246, 0.18) 0%, transparent 60%),
            radial-gradient(ellipse 60% 50% at 80% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 55%),
            radial-gradient(ellipse 50% 40% at 60% 30%, rgba(16, 185, 129, 0.08) 0%, transparent 50%);
        pointer-events: none;
        z-index: 0;
    }

    /* Moving grid overlay */
    #headless-wrapper::after {
        content: '';
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
        z-index: 0;
        animation: gridPan 25s linear infinite;
    }

    @keyframes gridPan {
        0%   { background-position: 0 0, 0 0; }
        100% { background-position: 60px 60px, 60px 60px; }
    }

    /* ===== PAGE LAYOUT ===== */
    .rsp-page {
        position: relative;
        z-index: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 48px 20px;
    }

    /* ===== HEADER ===== */
    .rsp-header {
        text-align: center;
        margin-bottom: 52px;
    }

    .rsp-logo-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 32px;
    }

    .rsp-logo-wrap img {
        max-height: 44px;
        filter: brightness(0) invert(1) drop-shadow(0 0 20px rgba(99, 102, 241, 0.5));
    }

    .rsp-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(99, 102, 241, 0.35);
        border-radius: 100px;
        padding: 5px 14px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #a5b4fc;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 18px;
        backdrop-filter: blur(8px);
    }

    .rsp-badge i {
        font-size: 0.8rem;
        color: #818cf8;
    }

    .rsp-title {
        font-family: 'Poppins', sans-serif;
        font-size: clamp(1.8rem, 4vw, 2.6rem);
        font-weight: 800;
        color: #f1f5f9;
        line-height: 1.15;
        margin-bottom: 10px;
        letter-spacing: -0.02em;
    }

    .rsp-title span {
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 50%, #34d399 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .rsp-subtitle {
        font-size: 1rem;
        color: #64748b;
        margin: 0;
        font-weight: 400;
        letter-spacing: 0.01em;
    }

    /* ===== ERROR ALERT ===== */
    .rsp-alert {
        width: 100%;
        max-width: 1100px;
        margin-bottom: 24px;
    }

    .rsp-alert .alert {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #fca5a5;
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }

    .rsp-alert .btn-close {
        filter: invert(1) opacity(0.7);
    }

    /* ===== CARDS GRID ===== */
    .rsp-grid {
        width: 100%;
        max-width: 1100px;
    }

    /* ===== ROLE CARD ===== */
    .role-card-outer {
        height: 100%;
    }

    .role-card {
        position: relative;
        border-radius: 20px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        transition: transform 0.22s cubic-bezier(.22,1,.36,1),
                    box-shadow 0.22s ease,
                    border-color 0.22s ease;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }

    .role-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, transparent 60%);
        border-radius: inherit;
        pointer-events: none;
    }

    /* Top shimmer line */
    .role-card::after {
        content: '';
        position: absolute;
        top: 0; left: 15%; right: 15%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
        border-radius: 100px;
        transition: opacity 0.22s ease;
        opacity: 0;
    }

    .role-card:hover {
        transform: translateY(-6px) scale(1.01);
        box-shadow:
            0 20px 60px rgba(0, 0, 0, 0.45),
            0 0 0 1px rgba(255,255,255,0.12) inset;
        border-color: rgba(255, 255, 255, 0.16);
    }

    .role-card:hover::after {
        opacity: 1;
    }

    .role-card.active-role {
        border-color: rgba(52, 211, 153, 0.4);
        box-shadow:
            0 8px 40px rgba(52, 211, 153, 0.12),
            0 0 0 1px rgba(52, 211, 153, 0.2) inset;
    }

    .role-card.active-role::after {
        background: linear-gradient(90deg, transparent, rgba(52, 211, 153, 0.4), transparent);
        opacity: 1;
    }

    /* Glow blob per card (set via inline style) */
    .role-card-glow {
        position: absolute;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        top: -30px;
        right: -30px;
        filter: blur(40px);
        opacity: 0.18;
        pointer-events: none;
        transition: opacity 0.22s ease;
    }

    .role-card:hover .role-card-glow {
        opacity: 0.3;
    }

    /* Card inner */
    .role-card-body {
        position: relative;
        z-index: 1;
        padding: 28px 24px 22px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    /* Icon */
    .role-icon-wrap {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 18px;
        flex-shrink: 0;
        position: relative;
    }

    .role-icon-wrap::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        border: 1px solid rgba(255,255,255,0.15);
    }

    /* Role title & label */
    .role-card-label {
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-bottom: 6px;
        opacity: 0.6;
        font-family: 'Inter', sans-serif;
    }

    .role-card-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: #e2e8f0;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .role-card-desc {
        font-size: 0.8rem;
        color: #475569;
        line-height: 1.5;
        flex: 1;
        margin-bottom: 20px;
    }

    /* Active badge */
    .active-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(52, 211, 153, 0.12);
        border: 1px solid rgba(52, 211, 153, 0.3);
        border-radius: 100px;
        padding: 3px 10px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #6ee7b7;
        margin-bottom: 14px;
        letter-spacing: 0.04em;
        width: fit-content;
    }

    .active-pill i {
        font-size: 0.75rem;
    }

    /* CTA Button */
    .role-card-footer {
        margin-top: auto;
    }

    .btn-role {
        width: 100%;
        padding: 10px 18px;
        border-radius: 11px;
        font-size: 0.83rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: all 0.18s ease;
        font-family: 'Inter', sans-serif;
        letter-spacing: 0.01em;
        position: relative;
        overflow: hidden;
    }

    .btn-role::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0);
        transition: background 0.18s ease;
    }

    .btn-role:hover::before {
        background: rgba(255,255,255,0.07);
    }

    .btn-role-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        color: #fff;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
    }

    .btn-role-primary:hover {
        box-shadow: 0 6px 22px rgba(99, 102, 241, 0.45);
        transform: translateY(-1px);
    }

    .btn-role-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: #fff;
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.28);
    }

    .btn-role-success:hover {
        box-shadow: 0 6px 22px rgba(16, 185, 129, 0.4);
        transform: translateY(-1px);
    }

    /* ===== ICON BG COLORS ===== */
    .ibg-primary  { background: rgba(59,  130, 246, 0.18); color: #60a5fa; }
    .ibg-danger   { background: rgba(239, 68,  68,  0.18); color: #f87171; }
    .ibg-success  { background: rgba(16,  185, 129, 0.18); color: #34d399; }
    .ibg-info     { background: rgba(6,   182, 212, 0.18); color: #22d3ee; }
    .ibg-warning  { background: rgba(245, 158, 11,  0.18); color: #fbbf24; }
    .ibg-secondary{ background: rgba(100, 116, 139, 0.18); color: #94a3b8; }
    .ibg-dark     { background: rgba(148, 163, 184, 0.12); color: #cbd5e1; }
    .ibg-purple   { background: rgba(139, 92,  246, 0.18); color: #a78bfa; }
    .ibg-pink     { background: rgba(236, 72,  153, 0.18); color: #f472b6; }

    .glow-primary  { background: #3b82f6; }
    .glow-danger   { background: #ef4444; }
    .glow-success  { background: #10b981; }
    .glow-info     { background: #06b6d4; }
    .glow-warning  { background: #f59e0b; }
    .glow-secondary{ background: #64748b; }
    .glow-dark     { background: #94a3b8; }
    .glow-purple   { background: #8b5cf6; }
    .glow-pink     { background: #ec4899; }

    /* Label colors */
    .lbl-primary  { color: #60a5fa; }
    .lbl-danger   { color: #f87171; }
    .lbl-success  { color: #34d399; }
    .lbl-info     { color: #22d3ee; }
    .lbl-warning  { color: #fbbf24; }
    .lbl-secondary{ color: #94a3b8; }
    .lbl-dark     { color: #cbd5e1; }
    .lbl-purple   { color: #a78bfa; }
    .lbl-pink     { color: #f472b6; }

    /* ===== FOOTER ===== */
    .rsp-footer {
        margin-top: 48px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .rsp-footer-email {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #334155;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 100px;
        padding: 6px 14px;
        backdrop-filter: blur(8px);
    }

    .rsp-footer-email i {
        color: #475569;
        font-size: 0.9rem;
    }

    .rsp-footer-email strong {
        color: #64748b;
        font-weight: 500;
    }

    .rsp-footer-sep {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #1e293b;
    }

    .rsp-logout-btn {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 0.82rem;
        color: #475569;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 100px;
        padding: 6px 14px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
        backdrop-filter: blur(8px);
    }

    .rsp-logout-btn:hover {
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.3);
        background: rgba(239, 68, 68, 0.06);
    }

    .rsp-logout-btn i {
        font-size: 0.9rem;
    }

    /* ===== CARD DESCRIPTIONS by role ===== */
    .role-desc-owner     { }
    .role-desc-admin     { }
    .role-desc-tenant    { }
    .role-desc-maintainer{ }
    .role-desc-shareholder{ }
    .role-desc-accountant{ }

    /* ===== ENTRANCE ANIMATION ===== */
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .role-card-outer {
        animation: cardIn 0.4s cubic-bezier(.22,1,.36,1) both;
    }

    .role-card-outer:nth-child(1) { animation-delay: 0.05s; }
    .role-card-outer:nth-child(2) { animation-delay: 0.10s; }
    .role-card-outer:nth-child(3) { animation-delay: 0.15s; }
    .role-card-outer:nth-child(4) { animation-delay: 0.20s; }
    .role-card-outer:nth-child(5) { animation-delay: 0.25s; }
    .role-card-outer:nth-child(6) { animation-delay: 0.30s; }
    .role-card-outer:nth-child(7) { animation-delay: 0.35s; }
    .role-card-outer:nth-child(8) { animation-delay: 0.40s; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 575.98px) {
        .rsp-header { margin-bottom: 36px; }
        .role-card-body { padding: 22px 18px 18px; }
        .role-icon-wrap { width: 46px; height: 46px; font-size: 1.25rem; }
        .rsp-title { font-size: 1.55rem; }
    }
</style>
@endpush

@section('content')
<div id="headless-wrapper">
    <div class="rsp-page">

        {{-- Header --}}
        <div class="rsp-header">
            <a href="/" class="rsp-logo-wrap">
                <img src="{{ getSettingImage('app_logo') }}" alt="{{ getOption('app_name') }}">
            </a>

            <div>
                <div class="rsp-badge">
                    <i class="ri-shield-keyhole-line"></i>
                    {{ __('Secure Access Portal') }}
                </div>
            </div>

            <h1 class="rsp-title">
                {{ __('Welcome back,') }}<br>
                <span>{{ $user->first_name }}</span>
            </h1>
            <p class="rsp-subtitle">{{ __('Choose a dashboard to enter') }}</p>
        </div>

        {{-- Flash error --}}
        @if(session('error'))
        <div class="rsp-alert">
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="ri-error-warning-line me-2 fs-5 flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        {{-- Role cards --}}
        <div class="rsp-grid">
            <div class="row g-4 justify-content-center">
                @foreach($roles as $role)
                    @php
                        $isActive = ($activeRole === $role['slug']);
                        $color    = $role['color'] ?? 'primary';

                        // Map Bootstrap color names to our dark-theme classes
                        $colorMap = [
                            'primary'   => 'primary',
                            'danger'    => 'danger',
                            'success'   => 'success',
                            'info'      => 'info',
                            'warning'   => 'warning',
                            'secondary' => 'secondary',
                            'dark'      => 'dark',
                        ];
                        $tc = $colorMap[$color] ?? 'primary';

                        // Short description per role slug
                        $descriptions = [
                            'owner'            => __('Manage properties, tenants & full system overview'),
                            'admin'            => __('System configuration & user administration'),
                            'tenant'           => __('View lease, payments & maintenance requests'),
                            'maintainer'       => __('Handle maintenance tasks & work orders'),
                            'shareholder'      => __('Portfolio overview, dividends & governance voting'),
                            'accountant'       => __('Financial reports, expenses & audit trails'),
                            'director'         => __('Executive KPIs, strategic overview & board reports'),
                            'finance_manager'  => __('Revenue analytics, budgets & financial controls'),
                            'landlord'         => __('Properties, rent rolls & occupancy metrics'),
                            'tenant_manager'   => __('Tenant relations, leases & communications'),
                            'auditor'          => __('Compliance checks, logs & internal audit'),
                            'compliance_officer'=> __('Regulatory compliance & policy management'),
                            'secretary'        => __('Meetings, documents & board communications'),
                        ];
                        $desc = $descriptions[$role['slug']] ?? __('Access your personalized dashboard');
                    @endphp

                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 role-card-outer">
                        <div class="role-card {{ $isActive ? 'active-role' : '' }}">

                            {{-- Glow blob --}}
                            <div class="role-card-glow glow-{{ $tc }}"></div>

                            <div class="role-card-body">

                                {{-- Icon --}}
                                <div class="role-icon-wrap ibg-{{ $tc }}">
                                    <i class="{{ $role['icon'] }}"></i>
                                </div>

                                {{-- Label --}}
                                <div class="role-card-label lbl-{{ $tc }}">
                                    {{ $role['label'] }}
                                </div>

                                {{-- Title --}}
                                <div class="role-card-title">{{ $role['label'] }} {{ __('Dashboard') }}</div>

                                {{-- Description --}}
                                <div class="role-card-desc">{{ $desc }}</div>

                                {{-- Active pill --}}
                                @if($isActive)
                                    <div class="active-pill">
                                        <i class="ri-checkbox-circle-fill"></i>
                                        {{ __('Currently Active') }}
                                    </div>
                                @endif

                                {{-- CTA --}}
                                <div class="role-card-footer">
                                    <form method="POST" action="{{ route('role.select') }}">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $role['slug'] }}">
                                        <button type="submit"
                                                class="btn-role {{ $isActive ? 'btn-role-success' : 'btn-role-primary' }}">
                                            @if($isActive)
                                                <i class="ri-layout-grid-2-line"></i>
                                                {{ __('Open Dashboard') }}
                                            @else
                                                <i class="ri-login-circle-line"></i>
                                                {{ __('Enter Dashboard') }}
                                                <i class="ri-arrow-right-line ms-auto" style="opacity:0.6;font-size:0.75rem;"></i>
                                            @endif
                                        </button>
                                    </form>
                                </div>

                            </div>{{-- /.role-card-body --}}
                        </div>{{-- /.role-card --}}
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="rsp-footer">
            <div class="rsp-footer-email">
                <i class="ri-user-3-line"></i>
                <strong>{{ $user->email }}</strong>
            </div>

            <div class="rsp-footer-sep"></div>

            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <a href="{{ route('logout') }}"
                   class="rsp-logout-btn"
                   onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="ri-logout-box-r-line"></i>
                    {{ __('Sign out') }}
                </a>
            </form>
        </div>

    </div>{{-- /.rsp-page --}}
</div>{{-- /#headless-wrapper --}}
@endsection
