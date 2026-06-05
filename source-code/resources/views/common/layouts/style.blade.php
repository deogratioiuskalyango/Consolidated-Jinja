<link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/libs/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">

<!-- Google Font CSS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('assets/libs/owl-carousel/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/owl-carousel/owl.theme.default.min.css') }}">

<link rel="stylesheet" href="{{ asset('assets/libs/venobox/venobox.min.css') }}">
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">

<!-- Dropzone css -->
<link href="{{ asset('assets/libs/dropzone/dropzone.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/extra-style.css') }}" rel="stylesheet">

<!-- RTL Style Start -->
@if (selectedLanguage()->rtl == 1)
    <link href="{{ asset('assets/css/rtl-style.css') }}" rel="stylesheet">
@endif
<!-- RTL Style End -->

<link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

<!-- FAVICONS -->
@php $favUrl = getSettingImage('app_fav_icon') . '?v=' . filemtime(public_path('favicon.ico')); @endphp
<link rel="icon" href="{{ $favUrl }}" type="image/png">
<link rel="shortcut icon" href="{{ $favUrl }}" type="image/png">
<link rel="apple-touch-icon" href="{{ $favUrl }}">


<!-- Sweetalert & Toastr -->
<link rel="stylesheet" href="{{asset('assets/sweetalert2/sweetalert2.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/dropify.css') }}">

<!-- Select2 -->
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />

<!-- Dual Light/Dark Theme — loaded last so it overrides all other styles -->
<link href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}" rel="stylesheet">
<!-- JCP UI enhancements — after theme.css so they take precedence -->
<link href="{{ asset('assets/css/jcp-ui.css') }}?v={{ filemtime(public_path('assets/css/jcp-ui.css')) }}" rel="stylesheet">

{{-- Admin custom brand colours — applied AFTER theme.css so they win --}}
@if (getOption('website_color_mode', 0) == ACTIVE)
<style>
:root {
    --primary-color:        {{ getOption('website_primary_color', '#3686FC') }};
    --secondary-color:      {{ getOption('website_secondary_color', '#8253FB') }};
    --button-primary-color: {{ getOption('button_primary_color', '#3686FC') }};
    --button-hover-color:   {{ getOption('button_hover_color', '#0063E6') }};
    --t-accent:   {{ getOption('website_primary_color', '#3686FC') }};
    --t-accent-2: {{ getOption('website_secondary_color', '#8253FB') }};
}
</style>
@endif
