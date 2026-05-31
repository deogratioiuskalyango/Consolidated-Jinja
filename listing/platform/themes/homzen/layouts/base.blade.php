<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>

        <link rel="preload" href="{{ Theme::asset()->url('fonts/icomoon.woff2') }}" as="font" type="font/woff2" crossorigin>

        <style>
            :root {
                --primary-color: {{ theme_option('primary_color', '#db1d23') }};
                --hover-color: {{ theme_option('hover_color', '#cd380f') }};
                --top-header-background-color: {{ theme_option('top_header_background_color', '#f7f7f7') }};
                --top-header-text-color: {{ theme_option('top_header_text_color', '#161e2d') }};
                --main-header-background-color: {{ theme_option('main_header_background_color', '#ffffff') }};
                --main-header-text-color: {{ theme_option('main_header_text_color', '#161e2d') }};
                --main-header-border-color: {{ theme_option('main_header_border_color', '#e4e4e4') }};
                --footer-text-color: {{ theme_option('footer_text_color', '#a3abb0') }};
                --footer-heading-color: {{ theme_option('footer_heading_color', '#ffffff') }};
                --footer-hover-color: {{ theme_option('footer_hover_color', '#cd380f') }};
                --map-marker-icon-image: url({{ theme_option('map_marker_image') ? RvMedia::getImageUrl(theme_option('map_marker_image')) : Theme::asset()->url('images/map-icon.png') }});
            }
        </style>

        {!! Theme::header() !!}
    </head>

    <body {!! Theme::bodyAttributes() !!}>
        {!! apply_filters(THEME_FRONT_BODY, null) !!}

        <div id="wrapper">
            <main class="clearfix">
                @yield('content')
            </main>
        </div>

        {!! Theme::footer() !!}

        {{-- Floating WhatsApp contact button --}}
        <a href="https://wa.me/256701673401?text=Hello%2C%20I%20am%20interested%20in%20Jinja%20Consolidated%20Properties" target="_blank" rel="noopener noreferrer" class="jcp-whatsapp-float" title="Chat with us on WhatsApp" aria-label="WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
        </a>
    </body>
</html>
