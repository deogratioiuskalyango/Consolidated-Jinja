<div class="pt-120 container">
    <div class="row">
        <div class="col-md-6">
            <div class="form-border-box">
                <form
                    class="form-horizontal"
                    role="form"
                    method="POST"
                    action="{{ route('customer.register.post') }}"
                >
                    <h2 class="normal"><span>{{ __('Register') }}</span></h2>
                    @csrf

                    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        <label
                            class="col-md-4 control-label"
                            for="first_name"
                        >{{ __('Name') }}</label>

                        <div class="col-md-12">
                            <input
                                class="form-control"
                                id="first_name"
                                name="first_name"
                                type="text"
                                value="{{ old('first_name') }}"
                                autofocus
                            >

                            @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                        <label
                            class="col-md-4 control-label"
                            for="last_name"
                        >{{ __('Last Name') }}</label>

                        <div class="col-md-12">
                            <input
                                class="form-control"
                                id="last_name"
                                name="last_name"
                                type="text"
                                value="{{ old('last_name') }}"
                                autofocus
                            >

                            @if ($errors->has('last_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    {!! apply_filters('hotel_customer_register_form_before', null) !!}

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label
                            class="col-md-4 control-label"
                            for="email"
                        >{{ __('E-Mail Address') }}</label>

                        <div class="col-md-12">
                            <input
                                class="form-control"
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                            >

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label
                            class="col-md-4 control-label"
                            for="password"
                        >{{ __('Password') }}</label>

                        <div class="col-md-12">
                            <input
                                class="form-control"
                                id="password"
                                name="password"
                                type="password"
                            >

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label
                            class="col-md-4 control-label"
                            for="password-confirm"
                        >{{ __('Password confirmation') }}</label>

                        <div class="col-md-12">
                            <input
                                class="form-control"
                                id="password-confirm"
                                name="password_confirmation"
                                type="password"
                            >

                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-12 mt-15">
                        <div class="form-group mb-25">
                            <label>
                                <span
                                    class="text-small">{{ __('Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our privacy policy.') }}</span>
                            </label>
                        </div>
                        <div class="form-group mb-25">
                            <label>
                                <input
                                    id="agree-terms-and-policy"
                                    name="agree_terms_and_policy"
                                    type="checkbox"
                                    value="1"
                                    @if (old('agree_terms_and_policy') == 1) checked @endif
                                >
                                <span class="text-small">{{ __('I agree to terms & Policy.') }}</span>
                            </label>
                        </div>
                    </div>

                    @if (is_plugin_active('captcha'))
                        @if (Captcha::isEnabled() && HotelHelper::getSetting('enable_recaptcha_in_register_page', 0))
                            <div class="form-group mb-3">
                                {!! Captcha::display() !!}
                            </div>
                        @endif

                        @if (HotelHelper::getSetting('enable_math_captcha_in_register_page', 0))
                            <div class="form-group mb-3">
                                {!! app('math-captcha')->input([
                                    'class' => 'form-control',
                                    'id' => 'math-group',
                                    'placeholder' => app('math-captcha')->getMathLabelOnly(),
                                ]) !!}
                            </div>
                        @endif
                    @endif

                    <div class="form-group mb-3">
                        <div class="col-md-12 col-md-offset-4">
                            <button
                                class="submit btn btn-md btn-black"
                                type="submit"
                            >
                                {{ __('Register') }}
                            </button>
                        </div>
                    </div>
                    <div class="text-center">
                        {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Hotel\Models\Customer::class) !!}
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
