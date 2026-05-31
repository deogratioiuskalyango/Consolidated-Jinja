<div class="footer-subscibe-area pt-120 pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="subscribe-text text-center">
                    <div class="footer-logo mb-45">
                        <img
                            src="{{ RvMedia::getImageUrl($shortcode->image) }}"
                            alt="{{ $shortcode->title }}"
                        >
                    </div>
                    <p>
                        {{ $shortcode->description }}
                    </p>
                    <form
                        class="subscribe-form mt-50"
                        action="{{ route('public.newsletter.subscribe') }}"
                    >
                        @csrf
                        <input
                            name="email"
                            type="email"
                            required
                            placeholder="{{ __('Enter you mail') }}"
                        >
                        @if (setting('enable_captcha') && is_plugin_active('captcha'))
                            <div class="input-group justify-content-center justify-content-lg-end mt-3">
                                {!! Captcha::display() !!}
                            </div>
                        @endif
                        <button type="submit">{{ __('Subscribe') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
