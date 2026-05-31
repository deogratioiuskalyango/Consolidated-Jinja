<header class="header-three">
    <div class="header-top">
        <div class="container container-custom-three">
            <div class="d-md-flex align-items-center justify-content-between">
                <p class="welcome-text">
                    @if (theme_option('address'))
                        {{ theme_option('address') }}
                    @endif
                </p>
                <ul class="header-top-info">
                    @if (theme_option('hotline'))
                        <li>
                            <a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a>
                        </li>
                    @endif
                    @if (theme_option('email'))
                        <li>
                            <a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="main-menu-area sticky-header">
        <div class="container container-custom-three">
            <div class="nav-container d-flex align-items-center justify-content-between">
                <div class="site-logo">
                    <a href="{{ route('public.single') }}">
                        <img
                            src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                            alt="{{ theme_option('site_title') }}"
                        >
                    </a>
                </div>
                <div class="nav-menu d-lg-flex align-items-center justify-content-between">

                    <div class="navbar-close">
                        <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                    </div>

                    <div class="menu-items">
                        {!! Menu::renderMenuLocation('header-menu', ['view' => 'menu']) !!}
                    </div>
                </div>

                <div class="menu-right-buttons">
                    <div class="toggle">
                        <a
                            id="offCanvasBtn"
                            href="#"
                        ><i class="fal fa-bars"></i></a>
                    </div>
                    <div class="navbar-toggler">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
