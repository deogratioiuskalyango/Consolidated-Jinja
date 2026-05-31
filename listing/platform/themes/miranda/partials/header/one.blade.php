<header class="header-absolute sticky-header @if (url()->current() == route('public.single')) header-two @else inner-page @endif">
    <div class="container container-custom-one">
        <div class="nav-container d-flex align-items-center justify-content-between breakpoint-on">
            <div class="nav-menu d-lg-flex align-items-center">

                <div class="navbar-close">
                    <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                </div>

                <div class="toggle">
                    <a
                        id="offCanvasBtn"
                        href="#"
                    ><i class="fal fa-bars"></i></a>
                </div>
                <div class="menu-items">
                    {!! Menu::renderMenuLocation('header-menu', ['view' => 'menu']) !!}
                </div>

                <div class="nav-pushed-item"></div>
            </div>

            <div class="site-logo">
                <a
                    class="main-logo"
                    href="{{ route('public.single') }}"
                >
                    <img
                        src="{{ RvMedia::getImageUrl(theme_option(url()->current() == route('public.single') ? 'logo_white' : 'logo')) }}"
                        alt="{{ theme_option('site_title') }}"
                    >
                </a>
                <a
                    class="sticky-logo"
                    href="{{ route('public.single') }}"
                >
                    <img
                        src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                        alt="{{ theme_option('site_title') }}"
                    >
                </a>
            </div>

            <div class="nav-push-item">
                <div class="header-info d-lg-flex align-items-center">
                    @if (theme_option('hotline'))
                        <div class="item">
                            <i class="fal fa-phone"></i>
                            <span>{{ __('Phone Number') }}</span>
                            <a href="tel:{{ theme_option('hotline') }}">
                                <h5 class="title">{{ theme_option('hotline') }}</h5>
                            </a>
                        </div>
                    @endif
                    @if (theme_option('email'))
                        <div class="item">
                            <i class="fal fa-envelope"></i>
                            <span>{{ __('Email Address') }}</span>
                            <a href="mailto:{{ theme_option('email') }}">
                                <h5 class="title">{{ theme_option('email') }}</h5>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="navbar-toggler">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</header>
