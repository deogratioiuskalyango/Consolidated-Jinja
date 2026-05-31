@if (!empty($url))
    <div
        class="video-wrap video-wrap-two video-about mb-60"
        @if ($background_image) style="background-image: url({{ RvMedia::getImageUrl($background_image) }});" @endif
    >
        <a
            class="popup-video"
            href="{{ $url }}"
        ><i class="fas fa-play"></i></a>
    </div>
@endif
