@if ($socials = \Botble\Theme\Supports\ThemeSupport::getSocialSharingButtons(url()->current(), SeoHelper::getDescription()))
    <div class="socials mb-3 pb-2 border-bottom w-100">
        <span>{{ $title }}:</span>

        <ul>
            @foreach($socials as $social)
                <li>
                    <a
                        href="{{ $social['url'] }}"
                        target="_blank"
                        aria-label="{{ __('Share on :social', ['social' => $social['name']]) }}"
                        @style(["background-color: {$social['background_color']}" => $social['background_color'], "color: {$social['color']}" => $social['color']])
                    >
                        {!! $social['icon'] !!}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="socials mb-3 pb-2 border-bottom w-100">
        <span>{{ $title }}:</span>
        <ul>
            <li>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}&title={{ $description }}" target="_blank" title="{{ __('Share on Facebook') }}"><i class="fab fa-facebook-f"></i></a>
            </li>
            <li>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&summary={{ rawurldecode($description) }}&source=Linkedin" title="{{ __('Share on Linkedin') }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            </li>
            <li>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ $description }}" target="_blank" title="{{ __('Share on Twitter') }}"><i class="fab fa-twitter"></i></a>
            </li>
        </ul>
    </div>
@endif

