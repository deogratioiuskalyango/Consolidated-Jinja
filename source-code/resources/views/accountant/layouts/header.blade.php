@php
    $accountant = auth()->user()->accountant ?? null;
    $unreadCount = 0;
@endphp
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <div class="navbar-brand-box">
                <a href="{{ route('accountant.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ getSettingImage('app_logo') }}" alt="logo-sm-light">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ getSettingImage('app_logo') }}" alt="logo-light">
                    </span>
                </a>
            </div>
            <button type="button" class="btn-sm px-3 font-24 header-item" id="vertical-menu-btn">
                <i class="ri-indent-decrease"></i>
            </button>
        </div>

        <div class="d-flex">
            {{-- Language Switcher --}}
            <div class="dropdown d-inline-block">
                <button type="button" class="header-item noti-icon" id="page-header-languages-dropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset(selectedLanguage()->icon) }}" alt="{{ selectedLanguage()->name ?? 'English' }}"
                        title="{{ selectedLanguage()->name ?? 'English' }}" class="rounded-circle avatar-xs fit-image">
                </button>
                <div class="dropdown-menu {{ selectedLanguage()->rtl == 1 ? 'dropdown-menu-start' : 'dropdown-menu-end' }}"
                    aria-labelledby="page-header-languages-dropdown">
                    <div>
                        @foreach (languages() as $language)
                            <a href="{{ route('local', $language->code) }}" class="dropdown-item"
                                title="{{ $language->code }}">
                                <div class="d-flex">
                                    <img src="{{ $language->icon }}" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                    <div class="flex-1">{{ $language->name }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Notification Bell (display only — no dropdown for accountant yet) --}}
            <div class="d-inline-block">
                <button type="button" class="header-item noti-icon" disabled style="cursor:default;">
                    <i class="ri-notification-2-fill"></i>
                </button>
            </div>

            {{-- Dark Mode --}}
            @include('common.partials.dark-mode-toggle')

            {{-- Role Switcher --}}
            @include('common.partials.role-switcher')

            {{-- User Dropdown --}}
            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="header-item" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle avatar-xs fit-image header-profile-user"
                        src="{{ auth()->user()->image }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1 font-medium">{{ auth()->user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu {{ selectedLanguage()->rtl == 1 ? 'dropdown-menu-start' : 'dropdown-menu-end' }}"
                    aria-labelledby="page-header-user-dropdown">
                    <a class="dropdown-item" href="{{ route('accountant.profile') }}">
                        <i class="ri-user-line align-middle me-1"></i> {{ __('Profile') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}">
                        <i class="ri-shut-down-line align-middle me-1"></i> {{ __('Logout') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
