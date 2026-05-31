<section class="flat-section platform-auth">
    <div class="platform-auth__shell">

        {{-- LEFT: Brand panel --}}
        <div class="platform-auth__intro">

            {{-- Logo --}}
            <a href="{{ BaseHelper::getHomepageUrl() }}" class="auth-brand-logo">
                <span class="auth-brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/></svg>
                </span>
                <span class="auth-brand-name">Jinja Consolidated<br><strong>Properties</strong></span>
            </a>

            <div class="auth-intro-content">
                <span class="auth-eyebrow">{{ __('Unified platform access') }}</span>
                <h2 class="auth-intro-title">{{ __('One account for everything in Jinja') }}</h2>
                <p class="auth-intro-desc">{{ __('Sign in to manage rental listings, book rooms, purchase hardware, submit quotations, and track your vendor or customer activity.') }}</p>

                <ul class="auth-feature-list">
                    <li>
                        <span class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('Properties & Rooms') }}</strong>
                            <span>{{ __('List or book rentals and short stays') }}</span>
                        </div>
                    </li>
                    <li>
                        <span class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('Hardware & Quotations') }}</strong>
                            <span>{{ __('Buy materials or request project quotes') }}</span>
                        </div>
                    </li>
                    <li>
                        <span class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('Vendor Store') }}</strong>
                            <span>{{ __('Manage products, orders and revenue') }}</span>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Bottom contact strip --}}
            <div class="auth-intro-contact">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z"/></svg>
                <span>{{ __('Need help? Call') }} <a href="tel:0701673401">0701 673401</a> &mdash; {{ __('Clive Rd, Jinja') }}</span>
            </div>
        </div>

        {{-- RIGHT: Login form --}}
        <div class="platform-auth__form">

            {{-- Logo (mobile only) --}}
            <a href="{{ BaseHelper::getHomepageUrl() }}" class="auth-mobile-logo">
                {{ Theme::getLogoImage(maxHeight: 36) }}
            </a>

            <h2 class="auth-form-title">{{ __('Sign in') }}</h2>
            <p class="auth-form-subtitle">{{ __('Welcome back. Enter your credentials to continue.') }}</p>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('customer.login.post') }}">
                @csrf

                <div class="auth-field">
                    <label for="email">{{ __('Email or username') }}</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </span>
                        <input id="email" type="text" name="email" value="{{ old('email') }}" class="form-control auth-input" autocomplete="username" required autofocus placeholder="{{ __('your@email.com') }}">
                    </div>
                </div>

                <div class="auth-field">
                    <div class="auth-field-header">
                        <label for="password">{{ __('Password') }}</label>
                        <a href="{{ route('public.account.password.request') }}" class="auth-forgot-link">{{ __('Forgot password?') }}</a>
                    </div>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </span>
                        <input id="password" type="password" name="password" class="form-control auth-input" autocomplete="current-password" required placeholder="{{ __('Enter your password') }}">
                    </div>
                </div>

                <label class="auth-remember">
                    <input type="checkbox" name="remember" value="1" class="auth-checkbox">
                    <span>{{ __('Keep me signed in') }}</span>
                </label>

                <button type="submit" class="tf-btn primary w-100 auth-submit-btn">
                    {{ __('Sign in to your account') }}
                </button>
            </form>

            <div class="auth-divider"><span>{{ __('New to Jinja Consolidated Properties?') }}</span></div>

            <div class="auth-register-links">
                <a href="{{ route('public.account.register') }}" class="auth-register-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                    {{ __('Create a property account') }}
                </a>
                @if (is_plugin_active('ecommerce'))
                    <a href="{{ route('customer.register') }}" class="auth-register-btn auth-register-btn--secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                        {{ __('Create a customer account') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
