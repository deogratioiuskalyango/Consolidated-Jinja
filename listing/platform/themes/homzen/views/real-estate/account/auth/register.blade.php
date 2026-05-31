@php
    Theme::set('breadcrumbEnabled', 'no');
@endphp

<section class="flat-section platform-auth">
    <div class="platform-auth__shell">

        {{-- LEFT: Brand intro panel --}}
        <div class="platform-auth__intro">
            <a href="{{ BaseHelper::getHomepageUrl() }}" class="auth-brand-logo">
                <span class="auth-brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/></svg>
                </span>
                <span class="auth-brand-name">Jinja Consolidated<br><strong>Properties</strong></span>
            </a>
            <div class="auth-intro-content">
                <span class="auth-eyebrow">{{ __('Join the platform') }}</span>
                <h2 class="auth-intro-title">{{ __('List, sell, and manage in Jinja') }}</h2>
                <p class="auth-intro-desc">{{ __('Create your account to list rental properties, open a hardware vendor store, book rooms, or manage quotation requests across Eastern Uganda.') }}</p>
                <ul class="auth-feature-list">
                    <li>
                        <span class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('Publish rental properties') }}</strong>
                            <span>{{ __('List homes, rooms, land, and farm stays for rent') }}</span>
                        </div>
                    </li>
                    <li>
                        <span class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('Sell hardware materials') }}</strong>
                            <span>{{ __('Open a vendor store and manage orders and revenue') }}</span>
                        </div>
                    </li>
                    <li>
                        <span class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('Free to create an account') }}</strong>
                            <span>{{ __('Browse listings, contact agents, and enquire for free') }}</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="auth-intro-contact">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z"/></svg>
                <span>{{ __('Need help? Call') }} <a href="tel:0701673401">0701 673401</a> &mdash; {{ __('Clive Rd, Jinja') }}</span>
            </div>
        </div>

        {{-- RIGHT: Registration form --}}
        <div class="platform-auth__form platform-auth__form--register">
            <a href="{{ BaseHelper::getHomepageUrl() }}" class="auth-mobile-logo">
                {{ Theme::getLogoImage(maxHeight: 36) }}
            </a>
            <h2 class="auth-form-title">{{ __('Create your account') }}</h2>
            <p class="auth-form-subtitle">{{ __('Join thousands of property owners, vendors, and buyers on the Jinja platform.') }}</p>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                </div>
            @endif

            <div class="register-form-wrapper">
                {!! $form->renderForm() !!}
            </div>

            <p class="auth-signin-link">{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Sign in') }}</a></p>
        </div>

    </div>
</section>
