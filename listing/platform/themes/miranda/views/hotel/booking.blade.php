@if (is_plugin_active('payment'))
    <link
        href="{{ asset('vendor/core/plugins/payment/css/payment.css') }}?v=1.0.3"
        rel="stylesheet"
    >
    <script src="{{ asset('vendor/core/plugins/payment/js/payment.js') }}?v=1.0.3"></script>

    {!! apply_filters(PAYMENT_FILTER_HEADER_ASSETS, null) !!}
@endif

<section
    class="breadcrumb-area"
    style="background-image: url({{ theme_option('rooms_banner') ? RvMedia::getImageUrl(theme_option('rooms_banner')) : Theme::asset()->url('img/bg/banner.jpg') }});"
>
    <div class="container">
        <div class="breadcrumb-text">
            <h2 class="page-title">{{ __('Booking') }}</h2>
            {!! Theme::partial('breadcrumb') !!}
        </div>
    </div>
</section>

<section class="pt-120 pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="booking-form-body room-details">
                    <form
                        class="booking-form-main payment-checkout-form"
                        action="{{ route('public.booking.checkout') }}"
                        method="POST"
                    >
                        @csrf
                        <input
                            name="token"
                            type="hidden"
                            value="{{ $token }}"
                        >
                        <input
                            name="amount"
                            type="hidden"
                            value="{{ $total }}"
                        >
                        <input
                            name="room_id"
                            type="hidden"
                            value="{{ $room->id }}"
                        >
                        <input
                            name="start_date"
                            type="hidden"
                            value="{{ $startDate->format('d-m-Y') }}"
                        >
                        <input
                            name="end_date"
                            type="hidden"
                            value="{{ $endDate->format('d-m-Y') }}"
                        >
                        <input
                            name="adults"
                            type="hidden"
                            value="{{ $adults }}"
                        >
                        <input
                            name="currency"
                            type="hidden"
                            value="{{ strtoupper(get_application_currency()->title) }}"
                        >
                        <input
                            name="currency_id"
                            type="hidden"
                            value="{{ get_application_currency_id() }}"
                        >
                        @if (is_plugin_active('paypal'))
                            <input
                                name="callback_url"
                                type="hidden"
                                value="{{ route('payments.paypal.status') }}"
                            >
                        @endif

                        <input
                            name="number_of_guests"
                            type="hidden"
                            value="{{ $adults }}"
                        >

                        <h3 class="mb-20">{{ __('Add Extra Services') }}</h3>
                        <div class="room-booking-form p-0 mb-20">
                            @php
                                $chunks = $services->chunk(ceil($services->count() / 2));
                            @endphp
                            <div class="row">
                                @if (count($chunks) > 0)
                                    <div class="col-md-6">
                                        @foreach ($chunks[0] as $service)
                                            <div class="form-group mb-20 custom-checkbox">
                                                <label for="service_{{ $service->id }}">
                                                    <input
                                                        class="service-item"
                                                        id="service_{{ $service->id }}"
                                                        name="services[]"
                                                        type="checkbox"
                                                        value="{{ $service->id }}"
                                                        @if (in_array($service->id, (array) old('services', []))) checked @endif
                                                    >
                                                    {{ $service->name }}
                                                    <em>({{ format_price($service->price) }})</em>
                                                    <span></span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if (count($chunks) > 1)
                                    <div class="col-md-6">
                                        @foreach ($chunks[1] as $service)
                                            <div class="form-group mb-20 custom-checkbox">
                                                <label for="service_{{ $service->id }}">
                                                    <input
                                                        class="service-item"
                                                        id="service_{{ $service->id }}"
                                                        name="services[{{ $service->id }}]"
                                                        type="checkbox"
                                                        value="{{ $service->id }}"
                                                    >
                                                    {{ $service->name }}
                                                    <em>({{ format_price($service->price) }})</em>
                                                    <span></span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <h3 class="mb-20">{{ __('Your Information') }}</h3>
                        <div class="room-booking-form p-0">

                            <p class="mb-20">{{ __('Required fields are followed by *') }}</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group input-group input-group-two mb-20">
                                        <label for="txt-first-name">{{ __('First Name') }} <span
                                                class="required">*</span></label>
                                        <input
                                            id="txt-first-name"
                                            name="first_name"
                                            type="text"
                                            value="{{ old('first_name', $customer) }}"
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group input-group input-group-two mb-20">
                                        <label for="txt-last-name">{{ __('Last Name') }} <span
                                                class="required">*</span></label>
                                        <input
                                            id="txt-last-name"
                                            name="last_name"
                                            type="text"
                                            value="{{ old('last_name', $customer) }}"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group input-group input-group-two mb-20">
                                        <label for="txt-email">{{ __('Email') }} <span
                                                class="required">*</span></label>
                                        <input
                                            id="txt-email"
                                            name="email"
                                            type="email"
                                            value="{{ old('email', $customer) }}"
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group input-group input-group-two mb-20">
                                        <label for="txt-phone">{{ __('Phone') }} <span
                                                class="required">*</span></label>
                                        <input
                                            id="txt-phone"
                                            name="phone"
                                            type="text"
                                            value="{{ old('phone', $customer) }}"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group input-group input-group-two mb-20">
                                        <label for="txt-address">{{ __('Address') }}</label>
                                        <input
                                            id="txt-address"
                                            name="address"
                                            type="text"
                                            value="{{ old('address', $customer) }}"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group input-group input-group-two mb-20">
                                        <label for="txt-city">{{ __('City') }}</label>
                                        <input
                                            id="txt-city"
                                            name="city"
                                            type="text"
                                            value="{{ old('city', $customer) }}"
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group input-group input-group-two mb-20">
                                        <label for="txt-country">{{ __('Country') }}</label>
                                        <input
                                            id="txt-country"
                                            name="country"
                                            type="text"
                                            value="{{ old('country', $customer) }}"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group input-group input-group-two mb-20">
                                        <label for="txt-zip">{{ __('Zip') }}</label>
                                        <input
                                            id="txt-zip"
                                            name="zip"
                                            type="text"
                                            value="{{ old('zip', $customer) }}"
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="form-group input-group input-group-two left-icon mb-20">
                                <label for="arrival_time">{{ __('Arrival') }}</label>
                                <select
                                    id="arrival_time"
                                    name="arrival_time"
                                >
                                    <option>{{ __('I do not know') }}</option>
                                    <option>12:00 - 1:00 {{ __('AM') }}</option>
                                    <option>1:00 - 2:00 {{ __('AM') }}</option>
                                    <option>2:00 - 3:00 {{ __('AM') }}</option>
                                    <option>3:00 - 4:00 {{ __('AM') }}</option>
                                    <option>4:00 - 5:00 {{ __('AM') }}</option>
                                    <option>5:00 - 6:00 {{ __('AM') }}</option>
                                    <option>6:00 - 7:00 {{ __('AM') }}</option>
                                    <option>7:00 - 8:00 {{ __('AM') }}</option>
                                    <option>8:00 - 9:00 {{ __('AM') }}</option>
                                    <option>9:00 - 10:00 {{ __('AM') }}</option>
                                    <option>10:00 - 11:00 {{ __('AM') }}</option>
                                    <option>11:00 - 12:00 {{ __('AM') }}</option>
                                    <option>12:00 - 1:00 {{ __('PM') }}</option>
                                    <option>1:00 - 2:00 {{ __('PM') }}</option>
                                    <option>2:00 - 3:00 {{ __('PM') }}</option>
                                    <option>3:00 - 4:00 {{ __('PM') }}</option>
                                    <option>4:00 - 5:00 {{ __('PM') }}</option>
                                    <option>5:00 - 6:00 {{ __('PM') }}</option>
                                    <option>6:00 - 7:00 {{ __('PM') }}</option>
                                    <option>7:00 - 8:00 {{ __('PM') }}</option>
                                    <option>8:00 - 9:00 {{ __('PM') }}</option>
                                    <option>9:00 - 10:00 {{ __('PM') }}</option>
                                    <option>10:00 - 11:00 {{ __('PM') }}</option>
                                    <option>11:00 - 12:00 {{ __('PM') }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-20">
                                <label for="requests">{{ __('Requests') }}</label>
                                <textarea
                                    id="requests"
                                    name="requests"
                                    rows="3"
                                    placeholder="{{ __('Write Something') }}..."
                                >{{ old('requests') }}</textarea>
                            </div>

                            @include('plugins/hotel::coupons.partials.form')

                            @if (is_plugin_active('payment') &&
                                    ($defaultPaymentMethod = PaymentMethods::getDefaultMethod()) &&
                                    get_payment_setting('status', $defaultPaymentMethod))
                                <div class="form-group mb-20">
                                    <label for="requests">{{ __('Payment method') }}</label>
                                    <ul class="list-group list_payment_method">
                                        {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                                            'amount' => $total,
                                            'currency' => strtoupper(get_application_currency()->title),
                                            'name' => $room->name,
                                            'selected' => PaymentMethods::getSelectedMethod(),
                                            'default' => $defaultPaymentMethod,
                                            'selecting' => PaymentMethods::getSelectingMethod(),
                                        ]) !!}

                                        {!! PaymentMethods::render() !!}
                                    </ul>
                                </div>
                            @endif

                            @if (setting('enable_captcha') && is_plugin_active('captcha'))
                                <div class="form-group mb-20">
                                    {!! Captcha::display() !!}
                                </div>
                            @endif

                            <div class="form-group mb-20 custom-checkbox d-block">
                                <label for="terms_conditions">
                                    <input
                                        id="terms_conditions"
                                        name="terms_conditions"
                                        type="checkbox"
                                        value="1"
                                        @if (old('terms_conditions') == 1) checked @endif
                                    > {{ __('Terms & conditions *') }}
                                    <span></span>
                                </label>
                            </div>
                            <div class="form-group mb-0">
                                <button
                                    class="main-btn btn-filled payment-checkout-btn"
                                    data-processing-text="{{ __('Processing. Please wait...') }}"
                                    data-error-header="{{ __('Error') }}"
                                    type="submit"
                                >{{ __('Checkout') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="room-details">
                    <div class="deatils-box">
                        <div class="room-rules clearfix mb-60 mt-60">
                            <h3 class="subtitle">{{ __('Hotel Rules') }}</h3>
                            <div class="room-rules-list">
                                {!! BaseHelper::clean(theme_option('hotel_rules')) !!}
                            </div>
                        </div>
                        <div class="cancellation-box clearfix mb-60">
                            <h3 class="subtitle">{{ __('Cancellation') }}</h3>
                            {!! BaseHelper::clean(theme_option('cancellation')) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-8 col-sm-10">
                <div class="sidebar">
                    <div style="font-family: 'Old Standard TT', serif; color: #fff; font-size: 14px;">
                        <div style="position: relative">
                            <span
                                class="room-type"
                                style="background-color: #bead8e; color: #fff; padding: 3px 5px; text-transform: uppercase; position: absolute; top: 15px; left: 15px;"
                            >{{ $room->name }}</span>
                            <img
                                src="{{ RvMedia::getImageUrl($room->image, '380x280', false, RvMedia::getDefaultImage()) }}"
                                alt="{{ $room->name }}"
                            >
                        </div>
                        <div style="padding: 20px; background-color: #bead8e;">
                            <div class="text-center mb-20">
                                <p>{{ __('YOUR RESERVATION') }}</p>
                            </div>
                            <div>
                                <p>{{ __('Check-In') }}: {{ $startDate->translatedFormat('l, d M, Y') }}</p>
                                <p>{{ __('Check-Out') }}: {{ $endDate->translatedFormat('l, d M, Y') }}</p>
                                <p>{{ __('Number of guests') }}: {{ $adults }}</p>
                                <p>{{ __('Price') }}: <span class="amount-text">{{ format_price($amount) }}</span></p>
                                <p>{{ __('Discount') }}: <span class="discount-text">{{ format_price(0) }}</span></p>
                                <p>{{ __('Tax') }}: <span class="tax-text">{{ format_price($taxAmount) }}</span></p>
                            </div>
                        </div>
                        <div
                            class="pb-20 pt-20 text-center"
                            style="font-size: 30px; background-color: #151516;"
                        >
                            <p>{{ __('Total') }}: <span class="total-amount-text">{{ format_price($total) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if (is_plugin_active('payment'))
    {!! apply_filters(PAYMENT_FILTER_FOOTER_ASSETS, null) !!}

    <script
        type="text/javascript"
        src="{{ asset('vendor/core/core/js-validation/js/js-validation.js') }}"
    ></script>
    {!! JsValidator::formRequest(\Botble\Hotel\Http\Requests\CheckoutRequest::class) !!}
@endif
