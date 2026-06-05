<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('common.layouts.meta')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ getOption('app_name') . ' - ' . @$pageTitle }}</title>
    @include('common.layouts.style')
    <link href="{{ asset('assets/css/shareholder-ui.css?v=' . filemtime(public_path('assets/css/shareholder-ui.css'))) }}" rel="stylesheet">
    @stack('style')
</head>
<body class="{{ selectedLanguage()->rtl == 1 ? 'direction-rtl' : 'direction-ltr' }}">
    <script>(function(){var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme:dark)').matches)){document.body.setAttribute('data-bs-theme','dark');}})();</script>
    @if (getOption('app_preloader_status') == 1)
        <div id="preloader"><div id="preloaderInner"><img src="{{ getSettingImage('app_preloader') }}" alt="img"></div></div>
    @endif
    <div id="layout-wrapper">
        @include('shareholder.layouts.header')
        @include('shareholder.layouts.sidebar')

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    {{-- Bottom nav OUTSIDE layout-wrapper so position:fixed is relative to viewport --}}
    @include('shareholder.layouts.bottom-nav')

    @include('common.layouts.script')
    @stack('script')
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <input type="hidden" id="deleteConfirmButtonText" value="{{__('Yes, Delete It!')}}">
    <input type="hidden" id="cancelButtonText" value="{{__('Cancel')}}">
</body>
</html>
