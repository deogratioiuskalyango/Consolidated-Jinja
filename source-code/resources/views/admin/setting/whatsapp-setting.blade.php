@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ __('Settings') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="#">{{ __('Settings') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="settings-page-layout-wrap position-relative">
                    <div class="row">
                        @include('admin.setting.sidebar')
                        <div class="col-md-12 col-lg-8 col-xxl-9">
                            <div class="account-settings-rightside bg-off-white theme-border radius-4 p-25">

                                {{-- Header --}}
                                <div class="account-settings-title border-bottom mb-20 pb-20">
                                    <div class="d-flex align-items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 48 48">
                                            <radialGradient id="wa-bg" cx="11.787" cy="36.213" r="38.71" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#57d163"/>
                                                <stop offset=".48" stop-color="#23b33a"/>
                                                <stop offset=".99" stop-color="#1f961f"/>
                                            </radialGradient>
                                            <path fill="url(#wa-bg)" d="M24 4C13 4 4 13 4 24c0 3.7 1 7.2 2.9 10.2L4 44l10.1-2.9C17.1 42.9 20.5 44 24 44c11 0 20-9 20-20S35 4 24 4z"/>
                                            <path fill="#fff" d="M33.5 28.8c-.4-.2-2.5-1.2-2.9-1.4-.4-.2-.7-.2-1 .2-.3.4-1.1 1.4-1.4 1.7-.3.3-.5.4-1 .1s-1.9-.7-3.6-2.2c-1.3-1.2-2.2-2.6-2.5-3-.3-.4 0-.7.2-.9.2-.2.4-.5.7-.8.2-.3.3-.5.5-.8.2-.3.1-.6 0-.8-.1-.2-1-2.5-1.4-3.4-.4-.9-.8-.8-1-.8h-.9c-.3 0-.8.1-1.2.6-.4.5-1.6 1.5-1.6 3.7s1.6 4.3 1.9 4.6c.2.3 3.2 4.9 7.8 6.9 1.1.5 1.9.7 2.6.9 1.1.3 2.1.3 2.9.2.9-.1 2.7-1.1 3.1-2.2.4-1.1.4-2 .3-2.2-.1-.2-.5-.4-.9-.6z"/>
                                        </svg>
                                        <h4 class="mb-0">{{ $pageTitle }}</h4>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0">
                                        {{ __('Connect your self-hosted') }}
                                        <a href="https://github.com/rmyndharis/OpenWA" target="_blank">OpenWA</a>
                                        {{ __('gateway to send WhatsApp messages for notices, KYC alerts, rent reminders, and shareholder notifications.') }}
                                    </p>
                                </div>

                                {{-- Form --}}
                                <form action="{{ route('admin.setting.general-setting-env.update') }}"
                                      method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="settings-inner-box bg-white theme-border radius-4 mb-25 p-20">

                                        {{-- Enabled --}}
                                        <div class="row mb-3">
                                            <div class="col-xl-6">
                                                <label class="label-text-title color-heading font-medium mb-2">{{ __('Status') }}</label>
                                                <select name="OPENWA_ENABLED" class="form-control">
                                                    <option value="false" {{ env('OPENWA_ENABLED','false') == 'false' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                                                    <option value="true"  {{ env('OPENWA_ENABLED','false') == 'true'  ? 'selected' : '' }}>{{ __('Enabled') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="label-text-title color-heading font-medium mb-2">{{ __('Country Code') }}</label>
                                                <input type="text" name="OPENWA_COUNTRY_CODE"
                                                       value="{{ env('OPENWA_COUNTRY_CODE', '62') }}"
                                                       class="form-control"
                                                       placeholder="62">
                                                <small class="text-muted">{{ __('Used to convert local numbers (e.g. 08xxx → 628xxx). 62 = Indonesia, 27 = South Africa, 44 = UK') }}</small>
                                            </div>
                                        </div>

                                        {{-- URL --}}
                                        <div class="row mb-3">
                                            <div class="col-xl-12">
                                                <label class="label-text-title color-heading font-medium mb-2">{{ __('OpenWA API URL') }}</label>
                                                <input type="url" name="OPENWA_URL"
                                                       value="{{ env('OPENWA_URL', 'http://localhost:2785') }}"
                                                       class="form-control"
                                                       placeholder="http://localhost:2785">
                                                <small class="text-muted">{{ __('The base URL of your running OpenWA instance (no trailing slash).') }}</small>
                                            </div>
                                        </div>

                                        {{-- API Key --}}
                                        <div class="row mb-3">
                                            <div class="col-xl-6">
                                                <label class="label-text-title color-heading font-medium mb-2">{{ __('API Key') }}</label>
                                                <input type="password" name="OPENWA_API_KEY"
                                                       value="{{ env('OPENWA_API_KEY', '') }}"
                                                       class="form-control"
                                                       placeholder="{{ __('Your OpenWA API key') }}">
                                                <small class="text-muted">{{ __('Found in the OpenWA dashboard under API Keys.') }}</small>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="label-text-title color-heading font-medium mb-2">{{ __('Session ID') }}</label>
                                                <input type="text" name="OPENWA_SESSION_ID"
                                                       value="{{ env('OPENWA_SESSION_ID', 'default') }}"
                                                       class="form-control"
                                                       placeholder="default">
                                                <small class="text-muted">{{ __('The session name you created in the OpenWA dashboard.') }}</small>
                                            </div>
                                        </div>

                                        {{-- Webhook info box --}}
                                        <div class="alert alert-info d-flex gap-2 mt-3" role="alert">
                                            <i class="ri-information-line flex-shrink-0 mt-1"></i>
                                            <div>
                                                <strong>{{ __('Incoming message webhook') }}</strong><br>
                                                {{ __('Point your OpenWA session webhook to:') }}
                                                <code class="d-block mt-1">{{ url('api/whatsapp/webhook') }}</code>
                                                {{ __('Incoming messages will be logged and can auto-create support tickets.') }}
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Save --}}
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="theme-btn">
                                                <i class="ri-save-line me-1"></i>{{ __('Save Settings') }}
                                            </button>
                                            <a href="{{ url('admin/test-whatsapp') }}" class="btn btn-outline-success ms-2" id="waTestBtn">
                                                <i class="ri-whatsapp-line me-1"></i>{{ __('Send Test Message') }}
                                            </a>
                                        </div>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
