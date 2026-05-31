<section
    class="banner-area banner-style-two"
    id="bannerSlider"
>
    @switch($shortcode->option)
        @case('theme')
            {!! Theme::partial('short-codes.home-banner.theme', compact('shortcode')) !!}
        @break

        @case('code')
            {!! Theme::partial('short-codes.home-banner.code', compact('shortcode')) !!}
        @break

        @default
            {!! Theme::partial('short-codes.home-banner.theme') !!}
    @endswitch
</section>
