{{-- One shortcode per page: youtube-channel.js binds to these IDs once. --}}
@php
    try {
        $cssUrl = \Botble\Theme\Facades\Theme::asset()->url('css/youtube-channel.css');
        $jsUrl = \Botble\Theme\Facades\Theme::asset()->url('js/youtube-channel.js');
    } catch (\Throwable) {
        $cssUrl = asset('themes/haft/css/youtube-channel.css');
        $jsUrl = asset('themes/haft/js/youtube-channel.js');
    }
@endphp
<link rel="stylesheet" href="{{ $cssUrl }}" />
<section class="youtube-channel-section w-layout-blockcontainer container w-container youtube-page-body">
    @if (! empty($title))
        <div class="youtube-channel-intro">
            <h2 class="heading-32">{{ $title }}</h2>
            @if (! empty($subtitle))
                <p class="youtube-channel-lead">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div id="yt-channel-header" class="youtube-channel-header" hidden></div>

    <div class="youtube-channel-toolbar">
        <label class="youtube-sort-wrap">
            <span class="youtube-sort-label">{{ __('Sort') }}</span>
            <select id="yt-sort" class="youtube-sort-select" aria-label="{{ __('Sort videos') }}">
                <option value="newest">{{ __('Newest') }}</option>
                <option value="oldest">{{ __('Oldest') }}</option>
                <option value="title">{{ __('Title') }}</option>
                <option value="views">{{ __('Views') }}</option>
            </select>
        </label>
        <div class="youtube-layout-group" role="group" aria-label="{{ __('Layout') }}">
            <button type="button" id="yt-layout-grid" class="secondary-button pd-35 w-button youtube-layout-btn is-active" aria-pressed="true">{{ __('Grid') }}</button>
            <button type="button" id="yt-layout-list" class="secondary-button pd-35 w-button youtube-layout-btn" aria-pressed="false">{{ __('List') }}</button>
            <button type="button" id="yt-layout-carousel" class="secondary-button pd-35 w-button youtube-layout-btn" aria-pressed="false">{{ __('Carousel') }}</button>
        </div>
        <button type="button" id="yt-theme-toggle" class="secondary-button pd-35 w-button">{{ __('Dark mode') }}</button>
    </div>

    <div class="youtube-channel-toolbar youtube-channel-toolbar--secondary">
        <input id="yt-search" type="search" class="w-input" placeholder="{{ __('Search videos…') }}" autocomplete="off" />
        <p id="yt-status" class="youtube-channel-status" role="status"></p>
    </div>

    <p id="yt-config-hint" class="youtube-config-hint" hidden></p>

    <div id="yt-grid" class="youtube-grid"></div>

    <div class="youtube-load-more-wrap">
        <button type="button" id="yt-load-more" class="primary-button pd-35 w-button" hidden>{{ __('Load more') }}</button>
    </div>
</section>
<script src="{{ $jsUrl }}" defer></script>
