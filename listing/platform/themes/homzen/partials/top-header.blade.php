<div class="top-header jcp-topbar">
    <div class="jcp-topbar__inner">
        <div class="jcp-topbar__left">
            <span class="jcp-topbar__item">
                <x-core::icon name="ti ti-map-pin" />
                <span>{{ __('Clive Rd, Jinja') }}</span>
            </span>
            <a class="jcp-topbar__item" href="tel:0701673401">
                <x-core::icon name="ti ti-phone" />
                <span>0701 673401</span>
            </a>
            <span class="jcp-topbar__item jcp-topbar__service">
                {{ __('Open 24 hours') }}
            </span>
        </div>

        <div class="jcp-topbar__right">
            <span class="jcp-topbar__service">{{ __('Land leasing, rentals, project management, hardware marketplace') }}</span>
            <a class="jcp-topbar__item jcp-topbar__whatsapp" href="https://wa.me/256701673401" target="_blank" rel="noopener noreferrer">
                <x-core::icon name="ti ti-brand-whatsapp" />
                <span>{{ __('WhatsApp') }}</span>
            </a>
            <a class="jcp-topbar__item" href="{{ url('/management/login') }}">
                <x-core::icon name="ti ti-building-community" />
                <span>{{ __('Management') }}</span>
            </a>
            @if (is_plugin_active('real-estate') && RealEstateHelper::isLoginEnabled())
                @auth('account')
                    <a class="jcp-topbar__item" href="{{ route('public.account.dashboard') }}">
                        <span>{{ auth('account')->user()->name }}</span>
                    </a>
                @else
                    <a class="jcp-topbar__item" href="{{ route('customer.login') }}">
                        <x-core::icon name="ti ti-user" />
                        <span>{{ __('Login / Register') }}</span>
                    </a>
                @endauth
            @endif
        </div>
    </div>
</div>
