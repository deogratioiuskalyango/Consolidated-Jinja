<x-core::layouts.base :body-attributes="['data-bs-theme' => 'dark']">
    <style>
        .jcp-admin-login {
            background: #0f1724;
            min-height: 100vh;
        }

        .jcp-admin-login__panel {
            background:
                linear-gradient(180deg, rgba(15, 23, 36, .92), rgba(15, 23, 36, .98)),
                radial-gradient(circle at top left, rgba(219, 29, 35, .22), transparent 36%);
            border-top: 4px solid #db1d23;
        }

        .jcp-admin-login__form {
            max-width: 430px;
        }

        .jcp-admin-login__logo {
            display: inline-flex;
            justify-content: center;
            margin-bottom: 2rem;
            width: 100%;
        }

        .jcp-admin-login__logo img {
            height: auto;
            max-height: 58px;
            max-width: 260px;
            width: auto;
        }

        .jcp-admin-login__visual {
            background-image:
                linear-gradient(90deg, rgba(15, 23, 36, .18), rgba(15, 23, 36, .42)),
                url("{{ asset('storage/hero1920/1.png') }}");
            background-position: center;
            background-size: cover;
        }

        .jcp-admin-login__caption {
            background: linear-gradient(180deg, transparent, rgba(15, 23, 36, .74));
            inset: auto 0 0;
            padding: 3rem;
        }

        .jcp-admin-login .form-control {
            min-height: 48px;
        }

        .jcp-admin-login .btn-primary {
            --bb-btn-bg: #db1d23;
            --bb-btn-border-color: #db1d23;
            --bb-btn-hover-bg: #b8171d;
            --bb-btn-hover-border-color: #b8171d;
            min-height: 48px;
        }

        @media (max-width: 991px) {
            .jcp-admin-login__panel {
                border-top: 0;
                min-height: 100vh;
            }

            .jcp-admin-login__form {
                max-width: 100%;
            }
        }
    </style>

    <main class="row g-0 flex-fill jcp-admin-login">
        <div class="col-12 col-lg-6 col-xl-4 d-flex flex-column justify-content-center jcp-admin-login__panel">
            <div class="container container-tight my-5 px-lg-5 jcp-admin-login__form">
                <div class="text-center">
                    <a class="jcp-admin-login__logo" href="{{ route('dashboard.index') }}">
                        <img src="{{ asset('storage/general/logo-light.png') }}" alt="{{ setting('admin_title', config('core.base.general.base_name')) }}">
                    </a>
                </div>

                @yield('content')
            </div>
        </div>
        <div class="position-relative col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            <div
                class="h-100 min-vh-100 jcp-admin-login__visual"
            ></div>
            <div class="position-absolute jcp-admin-login__caption">
                <div class="text-white me-5 mb-4">
                    <h1 class="mb-1">{{ setting('admin_title', config('core.base.general.base_name')) }}</h1>
                    <p class="mb-2">{{ __('Jinja operations console for properties, rooms, marketplace, quotations, and vendors.') }}</p>
                    <p class="mb-0">@include('core/base::partials.copyright')</p>
                </div>
            </div>
        </div>
    </main>
</x-core::layouts.base>
