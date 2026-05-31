@php
    ThemeOption::setOption('about-us', $shortcode->about_us ?: theme_option('about-us'));
    ThemeOption::setOption('hotline', $shortcode->hotline ?: theme_option('hotline'));
    ThemeOption::setOption('address', $shortcode->address ?: theme_option('address'));
    ThemeOption::setOption('email', $shortcode->email ?: theme_option('email'));
@endphp
