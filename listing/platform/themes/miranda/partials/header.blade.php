<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    {!! BaseHelper::googleFonts('https://fonts.googleapis.com/css2?family=' . urlencode(theme_option('primary_font', 'Archivo')) . ':ital,wght@0,400;0,500;0,600;0,700;1,400;1,700&family=' . urlencode(theme_option('secondary_font', 'Old Standard TT')) . ':ital,wght@0,400;0,700;1,400&family=' . urlencode(theme_option('tertiary_font', 'Roboto')) . ':wght@400;500;700&display=swap') !!}

    <style>
        :root {
            --color-1st: {{ theme_option('primary_color', '#bead8e') }};
            --primary-font: '{{ theme_option('primary_font', 'Archivo') }}', sans-serif;
            --secondary-font: '{{ theme_option('secondary_font', 'Old Standard TT') }}', sans-serif;
            --tertiary-font: '{{ theme_option('tertiary_font', 'Roboto') }}', sans-serif;
            --pattern-footer-dark: url({{ theme_option('pattern_footer_dark') ? RvMedia::getImageUrl(theme_option('pattern_footer_dark')) : Theme::asset()->url('img/icon/pattern-01.png') }});
            --pattern-footer-light: url({{ theme_option('pattern_footer_light') ? RvMedia::getImageUrl(theme_option('pattern_footer_light')) : Theme::asset()->url('img/icon/pattern-02.png') }});
        }
    </style>

    {!! Theme::header() !!}
</head>

<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>
    {!! apply_filters(THEME_FRONT_BODY, null) !!}
    <div id="alert-container"></div>

    @if (theme_option('preloader_enabled', 'no') == 'yes')
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="cssload-container">
                <div class="cssload-loading"><i></i><i></i><i></i><i></i></div>
            </div>
        </div>
    @endif
    {!! Theme::partial('header.' . theme_option('header_style', 'one')) !!}
    <div class="offcanvas-wrapper">
        <div class="offcanvas-overly"></div>
        <div class="offcanvas-widget">
            <a href="#" class="offcanvas-close"><i class="fal fa-times"></i></a>
            @if (is_plugin_active('hotel') && !theme_option('room_search_hide', false))
                <div class="widget search-widget">
                    <h5 class="widget-title">{{ __('Search room') }}</h5>
                    <form action="{{ route('public.rooms') }}">
                        <input type="text" name="q" value="{{ old('q', request()->query('q')) }}" placeholder="{{ __('Enter keyword') }}...">
                        <button type="submit"><i class="far fa-search"></i></button>
                    </form>
                </div>
            @endif

            <div class="widget about-widget">
                <h5 class="widget-title">{{ __('About us') }}</h5>
                <p>{{ theme_option('about-us') }}</p>
            </div>
            <div class="widget nav-widget">
                <h5 class="widget-title">{{ __('Our pages') }}</h5>
                {!! Menu::renderMenuLocation('side-menu', ['view' => 'menu']) !!}
            </div>
            <div class="widget social-link">
                <h5 class="widget-title">{{ __('Contact us') }}</h5>
                @if (theme_option('social_links'))
                    <ul>
                        @foreach (json_decode(theme_option('social_links'), true) as $socialLink)
                            @if (count($socialLink) == 3)
                                <li>
                                    <a href="{{ $socialLink[2]['value'] }}"
                                       title="{{ $socialLink[0]['value'] }}" target="_blank">
                                        <i class="{{ $socialLink[1]['value'] }}"></i>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
