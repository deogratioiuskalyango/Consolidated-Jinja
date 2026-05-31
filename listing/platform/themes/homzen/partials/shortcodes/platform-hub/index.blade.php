<section class="tf-section platform-hub">
    <div class="container">
        <div class="platform-hub__header">
            <div>
                <span class="platform-eyebrow">{{ $shortcode->subtitle ?: __('Stay, build, and sell') }}</span>
                <h2>{{ $shortcode->title ?: __('A rental marketplace with hardware commerce built in') }}</h2>
            </div>
            <p>{{ $shortcode->description ?: __('Guests can book stays and rooms, hosts can publish rental properties, and vendors can sell hardware materials through the marketplace with orders, revenues, and withdrawals.') }}</p>
        </div>

        <div class="platform-hub__cards">
            <a class="platform-hub-card" href="{{ route('public.properties') }}">
                <span>{{ number_format($stats['rentals']) }}</span>
                <strong>{{ __('Rental properties') }}</strong>
                <p>{{ __('Whole homes, apartments, serviced units, land, and rentable spaces managed by hosts.') }}</p>
            </a>
            <a class="platform-hub-card" href="{{ url('rooms') }}">
                <span>{{ number_format($stats['rooms']) }}</span>
                <strong>{{ __('Rooms and stays') }}</strong>
                <p>{{ __('Hotel-style inventory, room availability, booking checkout, and customer bookings.') }}</p>
            </a>
            <a class="platform-hub-card" href="{{ route('public.products') }}">
                <span>{{ number_format($stats['products']) }}</span>
                <strong>{{ __('Hardware marketplace') }}</strong>
                <p>{{ __('Materials, tools, finishes, and supplies sold by the platform and approved vendors.') }}</p>
            </a>
            <a class="platform-hub-card" href="{{ route('public.stores') }}">
                <span>{{ number_format($stats['stores']) }}</span>
                <strong>{{ __('Vendor stores') }}</strong>
                <p>{{ __('Independent suppliers can manage products, orders, revenues, shipments, and withdrawals.') }}</p>
            </a>
        </div>

        <div class="platform-hub__actions">
            <a class="tf-btn primary" href="{{ route('public.account.properties.index') }}">{{ __('List a rental') }}</a>
            <a class="tf-btn" href="{{ route('marketplace.vendor.become-vendor') }}">{{ __('Become a hardware vendor') }}</a>
            <a class="tf-btn" href="{{ route('public.hardware-quote') }}">{{ __('Request a quotation') }}</a>
        </div>
    </div>
</section>
