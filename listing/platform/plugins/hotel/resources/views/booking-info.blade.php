@php
    $route = $route ?? 'invoices.generate';
@endphp

@if ($booking)
    <div class="row">
        <div class="col-md-6">
            <p><strong>{{ __('Time') }}</strong>: <i>{{ $booking->created_at }}</i></p>
            <p><strong>{{ __('Full Name') }}</strong>: <i>{{ $booking->address->first_name }}
                    {{ $booking->address->last_name }}</i></p>
            <p><strong>{{ __('Email') }}</strong>: <i><a
                        class="booking-information-link"
                        href="mailto:{{ $booking->address->email }}"
                    >{{ $booking->address->email }}</a></i></p>
            @if ($booking->address->phone)
                <p><strong>{{ __('Phone') }}</strong>: <i><a
                            class="booking-information-link"
                            href="tel:{{ $booking->address->phone }}"
                        >{{ $booking->address->phone }}</a></i></p>
            @endif
            @if ($booking->address->full_address)
                <p><strong>{{ __('Address') }}</strong>: <i>{{ $booking->address->full_address }}</i></p>
            @endif
        </div>
        <div class="col-md-6">
            <p><strong>{{ __('Room') }}</strong>:
                <i>
                    @if ($booking->room->room->exists && ($room = $booking->room->room))
                        <a
                            class="booking-information-link"
                            href="{{ $room->url }}"
                            target="_blank"
                        >{{ $room->name }}</a>
                    @else
                        <span>{{ $booking->room->room_name }}</span>
                    @endif
                </i>
            </p>
            <p><strong>{{ __('Start Date') }}</strong>: <i>{{ $booking->room->start_date }}</i></p>
            <p><strong>{{ __('End Date') }}</strong>: <i>{{ $booking->room->end_date }}</i></p>
            @if ($booking->arrival_time)
                <p><strong>{{ __('Arrival Time') }}</strong>: <i>{{ $booking->arrival_time }}</i></p>
            @endif
            @if ($booking->requests)
                <p><strong>{{ __('Requests') }}</strong>: <i class="text-warning">{{ $booking->requests }}</i></p>
            @endif
            @if ($booking->number_of_guests)
                <p><strong>{{ __('Number of guests') }}</strong>: <i>{{ $booking->number_of_guests }}</i></p>
            @endif
        </div>
    </div>
    <br>
    <p><strong>{{ __('Room') }}</strong>:</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th
                    class="text-center"
                    style="width: 150px;"
                >{{ __('Image') }}</th>
                <th>{{ __('Name') }}</th>
                <th class="text-center">{{ __('Checkin Date') }}</th>
                <th class="text-center">{{ __('Checkout Date') }}</th>
                <th class="text-center">{{ __('Number of rooms') }}</th>
                <th class="text-center">{{ __('Price') }}</th>
                <th class="text-center">{{ __('Tax') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @if ($booking->room->room->exists && ($room = $booking->room->room))
                    <td
                        class="text-center"
                        style="width: 150px; vertical-align: middle"
                    >
                        <a
                            href="{{ $booking->room->room->url }}"
                            target="_blank"
                        >
                            <img
                                src="{{ RvMedia::getImageUrl($booking->room->room->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                alt="{{ $booking->room->room_name }}"
                                width="140"
                            >
                        </a>
                    </td>
                    <td style="vertical-align: middle"><a
                            class="booking-information-link"
                            href="{{ $booking->room->room->url }}"
                            target="_blank"
                        >{{ $booking->room->room->name }}</a></td>
                @else
                    <td>
                        <img
                            src="{{ RvMedia::getImageUrl($booking->room->room_image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                            alt="{{ $booking->room->room_name }}"
                            width="140"
                        >
                    </td>
                    <td style="vertical-align: middle">{{ $booking->room->room_name }}</td>
                @endif
                <td
                    class="text-center"
                    style="vertical-align: middle"
                >{{ $booking->room->start_date }}</td>
                <td
                    class="text-center"
                    style="vertical-align: middle"
                >{{ $booking->room->end_date }}</td>
                <td
                    class="text-center"
                    style="vertical-align: middle"
                >{{ $booking->room->number_of_rooms }}</td>
                <td
                    class="text-center"
                    style="vertical-align: middle"
                ><strong>{{ format_price($booking->room->price) }}</strong></td>
                <td
                    class="text-center"
                    style="vertical-align: middle"
                ><strong>{{ format_price($booking->tax_amount) }}</strong></td>
            </tr>
        </tbody>
    </table>
    <br>
    @if ($booking->services->count())
        <p><strong>{{ __('Services') }}</strong>:</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th class="text-center">{{ __('Price') }}</th>
                    <th class="text-center">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($booking->services->unique() as $service)
                    <tr>
                        <td style="vertical-align: middle">
                            {{ $service->name }}
                        </td>
                        <td
                            class="text-center"
                            style="vertical-align: middle"
                        >{{ format_price($service->price) }} x {{ $booking->room->number_of_rooms }}</td>
                        <td
                            class="text-center"
                            style="vertical-align: middle"
                        >{{ format_price($service->price * $booking->room->number_of_rooms) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    @endif
    <br>
    <p><strong>{{ __('Sub Total') }}</strong>: <span class="text-danger">{{ format_price($booking->sub_total) }}</span></p>
    <p><strong>{{ __('Discount Amount') }}</strong>: <span class="text-danger">{{ format_price($booking->coupon_amount) }}</span></p>
    <p><strong>{{ __('Tax Amount') }}</strong>: <span class="text-danger">{{ format_price($booking->tax_amount) }}</span></p>
    <p><strong>{{ __('Total Amount') }}</strong>: <span class="text-danger">{{ format_price($booking->amount) }}</span></p>
    @if (is_plugin_active('payment') && $booking->payment->id)
        @if (auth()->check())
            <p><strong>{{ __('Payment ID') }}</strong>: <a
                    href="{{ route('payment.show', $booking->payment->id) }}"
                    target="_blank"
                >{{ $booking->payment->charge_id }} <i class="fas fa-external-link-alt"></i></a></p>
        @endif
        <p><strong>{{ __('Payment method') }}</strong>: {{ $booking->payment->payment_channel->label() }}</p>
        <p><strong>{{ __('Payment status') }}</strong>: {!! $booking->payment->status->toHtml() !!}</p>

        @if (
            $booking->payment->payment_channel == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER &&
                $booking->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
            <p><strong>{{ __('Payment info') }}</strong>: <span class="text-warning">{!! BaseHelper::clean(get_payment_setting('description', $booking->payment->payment_channel)) !!}</span></p>
        @endif
    @endif

    @if ($displayBookingStatus ?? false)
        <p><strong>{{ __('Booking status') }}</strong>: {!! $booking->status->toHtml() !!}</p>
    @endif

    @if ((auth()->check() || $booking->customer_id) && ($invoiceId = $booking->invoice->id) && $route)
        <div class="row">
            <div class="form-group mt-20">
                <a
                    class="btn btn-primary btn-sm"
                    href="{{ route($route, ['id' => $invoiceId, 'type' => 'print']) }}"
                    target="_blank"
                >
                    <i class="fas fa-print"></i> {{ __('view Invoice') }}
                </a>
                <a
                    class="btn btn-primary btn-sm ms-2"
                    href="{{ route($route, ['id' => $invoiceId, 'type' => 'download']) }}"
                    target="_blank"
                >
                    <i class="fas fa-download"></i> {{ __('Download Invoice') }}
                </a>
            </div>
        </div>
    @endif
@endif
