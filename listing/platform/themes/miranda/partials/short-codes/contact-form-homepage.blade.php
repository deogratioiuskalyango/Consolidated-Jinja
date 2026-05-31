<section class="pt-115 pb-160">
    <div class="card border-light p-5">
        <div class="section-title text-lg-left text-center">
            <h2>{!! BaseHelper::clean($shortcode->title) !!}</h2>
            <span class="title-tag">{!! BaseHelper::clean($shortcode->subtitle) !!}</span>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="contact-form">
                    {!! Form::open(['route' => 'public.send.contact', 'class' => 'form--contact', 'method' => 'POST']) !!}

                    <div class="row">
                        {!! apply_filters('pre_contact_form', null) !!}

                        <div class="col-md-6">
                            <div class="input-group mb-30">
                                <span class="icon"><i class="far fa-user"></i></span>
                                <input
                                    id="contact_name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    placeholder="{{ __('Name') }}"
                                >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-30">
                                <span class="icon"><i class="far fa-envelope"></i></span>
                                <input
                                    id="contact_email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    placeholder="{{ __('Email') }}"
                                >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-30">
                                <span class="icon"><i class="fal fa-map-marker-alt"></i></span>
                                <input
                                    id="contact_address"
                                    name="address"
                                    type="text"
                                    value="{{ old('address') }}"
                                    placeholder="{{ __('Address') }}"
                                >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-30">
                                <span class="icon"><i class="far fa-phone"></i></span>
                                <input
                                    id="contact_phone"
                                    name="phone"
                                    type="text"
                                    value="{{ old('phone') }}"
                                    placeholder="{{ __('Phone') }}"
                                >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group mb-30">
                                <span class="icon"><i class="far fa-book"></i></span>
                                <input
                                    id="contact_subject"
                                    name="subject"
                                    type="text"
                                    value="{{ old('subject') }}"
                                    placeholder="{{ __('Subject') }}"
                                >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group textarea mb-30">
                                <span class="icon"><i class="far fa-pen"></i></span>
                                <textarea
                                    id="contact_content"
                                    name="content"
                                    rows="5"
                                    placeholder="{{ __('Message') }}"
                                >{{ old('content') }}</textarea>
                            </div>
                        </div>

                        @if (is_plugin_active('captcha'))
                            @if (setting('enable_captcha'))
                                <div class="col-md-12">
                                    <div class="input-group mb-30">
                                        {!! Captcha::display() !!}
                                    </div>
                                </div>
                            @endif

                            @if (setting('enable_math_captcha_for_contact_form', 0))
                                <div class="col-md-12">
                                    <div class="input-group mb-30">
                                        <span class="icon"><i class="far fa-calculator"></i></span>
                                        {!! app('math-captcha')->input(['id' => 'math-group', 'placeholder' => app('math-captcha')->label()]) !!}
                                    </div>
                                </div>
                            @endif
                        @endif

                        {!! apply_filters('after_contact_form', null) !!}

                        <div class="col-12">
                            <div class="input-group mb-30">
                                <button
                                    class="main-btn btn-filled"
                                    type="submit"
                                >{{ __('Get Free Quote') }}</button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <div
                                    class="contact-message contact-success-message"
                                    style="display: none"
                                ></div>
                                <div
                                    class="contact-message contact-error-message"
                                    style="display: none"
                                ></div>
                            </div>
                        </div>

                    </div>

                    {!! Form::close() !!}
                </div>
            </div>

            <div class="col-md-6">
                @switch($shortcode->block)
                    @case('map')
                        <div style="height: 100%; width: 100%; position: relative; text-align: right;">
                            <div style="height: 100%; width: 100%; overflow: hidden; background: none!important;">
                                <iframe
                                    src="https://maps.google.com/maps?q={{ addslashes($shortcode->address) }}%20&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                    width="100%"
                                    height="500"
                                    frameborder="0"
                                    scrolling="no"
                                    marginheight="0"
                                    marginwidth="0"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                ></iframe>
                            </div>
                        </div>
                    @break

                    @case('image')
                        <div style="width: 100%; position: relative; text-align: right;">
                            <img
                                src="{{ RvMedia::getImageUrl($shortcode->image) }}"
                                alt="{{ $shortcode->image }}"
                            >
                        </div>
                    @break
                @endswitch

            </div>

        </div>
    </div>
</section>
