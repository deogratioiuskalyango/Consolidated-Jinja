<div class="pt-120 container">
    <div class="row">
        <div class="col-md-6">
            <div class="form-border-box">
                <div class="form-border-box">
                    <form
                        method="POST"
                        action="{{ route('customer.login.post') }}"
                    >
                        <h2 class="normal"><span>{{ __('Login') }}</span></h2>
                        @csrf

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label
                                class="col-md-4 control-label"
                                for="email"
                            >{{ __('Name') }}</label>

                            <div class="col-md-12">
                                <input
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    type="text"
                                    value="{{ old('email') }}"
                                    autofocus
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
                                    type="text"
                                    value="{{ old('password') }}"
                                    autofocus
                                >

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="col-lg-3 col-6 mt-15">
                                <div class="form-group mb-25">
                                    <label class="cb-container">
                                        <input
                                            id="remember-check"
                                            name="remember"
                                            type="checkbox"
                                            value="1"
                                            @if (old('remember', 1)) checked @endif
                                        >
                                        <span class="text-small">{{ __('Remember me') }}</span>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6 mt-15">
                                <div class="form-group mb-25 text-end">
                                    <a
                                        class="font-xs color-grey-500"
                                        href="{{ route('customer.password.reset') }}"
                                    >
                                        {{ __('Forgot password?') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-field-wrapper">
                            <button
                                class="submit btn btn-md btn-black"
                                type="submit"
                            >
                                {{ __('Login') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-border-box">
                    <form>
                        <h2 class="normal"><span>{{ __('You are a new customer?') }}</span></h2>
                        <p>{{ __('Register here') }}</p>
                        <div class="form-field-wrapper">
                            <a
                                class="submit btn btn-md btn-color"
                                href="{{ route('customer.register') }}"
                            >
                                {{ __('Register a new account') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center">
                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Hotel\Models\Customer::class) !!}
            </div>
        </div>
    </div>
</div>
