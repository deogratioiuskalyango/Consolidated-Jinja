<header
    id="header"
    @class(['main-header', 'fixed-header' => theme_option('sticky_header_enabled', true), Theme::get('headerClass')])
>
    <div class="header-lower">
        <div class="row">
            <div class="col-lg-12">
                <div class="inner-container d-flex justify-content-between align-items-center">
                    <div class="logo-box">
                        <div class="logo">
                            <a href="{{ BaseHelper::getHomepageUrl() }}">
                                {{ Theme::getLogoImage(maxHeight: 44) }}
                            </a>
                        </div>
                    </div>
                    <div class="nav-outer">
                        <nav class="main-menu show navbar-expand-md">
                            <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                                {!! Menu::renderMenuLocation('main-menu', [
                                    'options' => ['class' => 'navigation clearfix'],
                                    'view' => 'main-menu',
                                ]) !!}
                            </div>
                        </nav>
                    </div>
                    <div class="header-account">
                        <div class="flat-bt-top platform-header-actions">
                            <a class="platform-header-link platform-header-link--management" href="{{ url('/management/login') }}">
                                <x-core::icon name="ti ti-building-community" />
                                <span>{{ __('Management') }}</span>
                            </a>
                            @if (is_plugin_active('marketplace'))
                                <a class="platform-header-link" href="{{ route('marketplace.vendor.become-vendor') }}">
                                    <x-core::icon name="ti ti-tools" />
                                    <span>{{ __('Sell Hardware') }}</span>
                                </a>
                            @endif
                            @if (is_plugin_active('real-estate'))
                                <a class="tf-btn primary" href="{{ route('public.account.properties.index') }}">
                                    {{ __('List your space') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="mobile-nav-toggler mobile-button">
                        <x-core::icon name="ti ti-menu-2" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="close-btn">
        <x-core::icon name="ti ti-x" />
    </div>

    <div class="mobile-menu">
        <div class="menu-backdrop"></div>
        <nav class="menu-box">
            <div class="nav-logo">
                <a href="{{ BaseHelper::getHomepageUrl() }}">
                    {{ Theme::getLogoImage(maxHeight: 44) }}
                </a>
            </div>
            <div class="bottom-canvas">
                @if (is_plugin_active('real-estate') && RealEstateHelper::isLoginEnabled())
                    <div class="mobile-add-listing-wrapper">
                        <a href="{{ url('/management/login') }}" class="mobile-add-listing-btn">
                            <div class="add-listing-icon-wrapper">
                                <x-core::icon name="ti ti-building-community" />
                            </div>
                            <div class="add-listing-text-wrapper">
                                <span class="add-listing-title">{{ __('Management') }}</span>
                                <span class="add-listing-subtitle">{{ __('Open the management system') }}</span>
                            </div>
                            <x-core::icon name="ti ti-arrow-right" class="add-listing-arrow" />
                        </a>
                        <a href="{{ route('public.account.properties.index') }}" class="mobile-add-listing-btn">
                            <div class="add-listing-icon-wrapper">
                                <x-core::icon name="ti ti-plus" />
                            </div>
                            <div class="add-listing-text-wrapper">
                                <span class="add-listing-title">{{ __('Submit Property') }}</span>
                                <span class="add-listing-subtitle">{{ __('List your property') }}</span>
                            </div>
                            <x-core::icon name="ti ti-arrow-right" class="add-listing-arrow" />
                        </a>
                    </div>

                    @auth('account')
                        @php $customer = auth('account')->user(); @endphp
                        <div class="mobile-user-wrapper">
                            <a href="{{ route('public.account.dashboard') }}" class="mobile-user-btn">
                                <div class="user-avatar-wrapper">
                                    {{ RvMedia::image($customer->avatar_url, $customer->name, 'thumb', attributes: ['class' => 'user-avatar']) }}
                                </div>
                                <div class="user-text-wrapper">
                                    <span class="user-name">{{ $customer->name }}</span>
                                    <span class="user-subtitle">{{ __('View dashboard') }}</span>
                                </div>
                                <x-core::icon name="ti ti-chevron-right" class="user-arrow" />
                            </a>
                        </div>
                    @else
                        <div class="mobile-signin-wrapper">
                            <a
                                @if (theme_option('use_modal_for_authentication', true))
                                    href="#modalLogin"
                                    data-bs-toggle="modal"
                                @else
                                    href="{{ route('public.account.login') }}"
                                @endif
                                class="mobile-signin-btn"
                            >
                                <div class="signin-icon-wrapper">
                                    <x-core::icon name="ti ti-user" />
                                </div>
                                <div class="signin-text-wrapper">
                                    <span class="signin-title">{{ __('Sign in') }}</span>
                                    <span class="signin-subtitle">{{ __('Access your account') }}</span>
                                </div>
                                <x-core::icon name="ti ti-chevron-right" class="signin-arrow" />
                            </a>
                        </div>
                    @endauth
                @endif

                <div class="menu-outer"></div>

                @php $mobileSwitcherStyle = theme_option('mobile_menu_switcher_style', 'inline'); @endphp

                <div class="mobi-icon-box">
                    @if (is_plugin_active('real-estate'))
                        @if (RealEstateHelper::isEnabledWishlist())
                            <div class="box">
                                <a href="{{ route('public.wishlist') }}">
                                    {{ __('My Wishlist') }}
                                    (<span data-bb-toggle="wishlist-count" class="fw-medium">0</span>)
                                </a>
                            </div>
                        @endif
                    @endif

                    @if ($mobileSwitcherStyle === 'dropdown')
                        @if (is_plugin_active('real-estate'))
                            <div class="box">
                                {!! Theme::partial('currency-switcher') !!}
                            </div>
                        @endif

                        @if ($languageSwitcher = Theme::partial('language-switcher'))
                            <div class="box">
                                {!! $languageSwitcher !!}
                            </div>
                        @endif
                    @endif

                    @if($hotline = theme_option('hotline'))
                        <div class="box d-flex align-items-center gap-2">
                            <x-core::icon name="ti ti-phone" style="width: 1.25rem; height: 1.25rem" />
                            <a href="tel:{{ $hotline }}" title="{{ __('Phone') }}">{{ $hotline }}</a>
                        </div>
                    @endif
                    @if($email = theme_option('email'))
                        <div class="box d-flex align-items-center gap-2">
                            <x-core::icon name="ti ti-mail" style="width: 1.25rem; height: 1.25rem" />
                            <a href="mailto:{{ $email }}" title="{{ __('Email') }}">{{ $email }}</a>
                        </div>
                    @endif
                </div>

                @if ($mobileSwitcherStyle === 'inline')
                    <div class="mobile-switchers">
                        @if (is_plugin_active('real-estate'))
                            {!! Theme::partial('currency-switcher-mobile') !!}
                        @endif
                        {!! Theme::partial('language-switcher-mobile') !!}
                    </div>
                @endif
            </div>
        </nav>
    </div>
</header>
