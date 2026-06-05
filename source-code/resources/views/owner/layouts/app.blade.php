<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('common.layouts.meta')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ getOption('app_name') . ' - ' . @$pageTitle }}</title>

    @include('common.layouts.style')
    @stack('style')
</head>

<body class="{{ selectedLanguage()->rtl == 1 ? 'direction-rtl' : 'direction-ltr' }}">
    <script>(function(){var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme:dark)').matches)){document.body.setAttribute('data-bs-theme','dark');}})();</script>

    @if (getOption('app_preloader_status') == 1)
        <div id="preloader">
            <div id="preloaderInner"><img src="{{ getSettingImage('app_preloader') }}" alt="img"></div>
        </div>
    @endif

    <div id="layout-wrapper">
        @include('owner.layouts.header')
        @include('owner.layouts.sidebar')
        @yield('content')
    </div>
    @include('owner.layouts.modal')
    @include('common.layouts.script')
    @stack('script')

    <!-- App Custom js -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <input type="hidden" id="topSearchRoute" value="{{ route('owner.top.search') }}">
    <input type="hidden" id="deleteConfirmButtonText" value="{{__('Yes, Delete It!')}}">
    <input type="hidden" id="cancelButtonText" value="{{__('Cancel')}}">
</body>

</html>
