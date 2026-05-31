<section
    class="room-gallery-cta"
    style="background-image: url({{ RvMedia::getImageUrl($background_image) }});"
>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="cta-text text-center">
                    <span>{!! BaseHelper::clean($title) !!}</span>
                    <h2>{!! BaseHelper::clean($description ?: $subtitle) !!}</h2>
                    @if ($button_text)
                        <ul class="mt-50">
                            <li
                                class="wow fadeInUp"
                                data-wow-delay=".3s"
                            ><a
                                    class="main-btn btn-filled"
                                    href="{{ $button_url }}"
                                >{{ $button_text }}</a></li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="rotate-images">
        @if ($first_image)
            <img
                class="rotate-image-one"
                src="{{ RvMedia::getImageUrl($first_image) }}"
                alt="Image"
            >
        @endif
        @if ($third_image)
            <img
                class="rotate-image-two"
                src="{{ RvMedia::getImageUrl($second_image) }}"
                alt="Image"
            >
        @endif
        @if ($third_image)
            <img
                class="rotate-image-three"
                src="{{ RvMedia::getImageUrl($third_image) }}"
                alt="Image"
            >
        @endif
    </div>
</section>
