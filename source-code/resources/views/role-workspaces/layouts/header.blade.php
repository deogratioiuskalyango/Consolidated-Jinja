<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex align-items-center">
            <div class="navbar-brand-box">
                <a href="{{ route('role.' . str_replace('_', '-', $role ?? 'director') . '.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ getSettingImage('app_logo') }}" alt="logo-sm">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ getSettingImage('app_logo') }}" alt="logo">
                    </span>
                </a>
            </div>
            {{-- Hamburger — visible on desktop, hidden on mobile (bottom nav used instead) --}}
            <button type="button" class="btn-sm px-3 font-24 header-item d-none d-lg-inline-flex" id="vertical-menu-btn">
                <i class="ri-indent-decrease"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-1">

            {{-- Role badge --}}
            <span class="d-none d-sm-inline-block badge bg-primary bg-opacity-10 text-primary" style="font-size:.75rem;padding:.35rem .7rem;">
                <i class="{{ $icon ?? 'ri-user-line' }} me-1"></i>{{ $label ?? 'Workspace' }}
            </span>

            {{-- Dark Mode --}}
            @include('common.partials.dark-mode-toggle')

            {{-- Role Switcher — hidden on xs to save space --}}
            <span class="d-none d-md-inline-block">
                @include('common.partials.role-switcher')
            </span>

            {{-- User Dropdown --}}
            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="header-item d-flex align-items-center gap-2" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle avatar-xs fit-image header-profile-user"
                        src="{{ auth()->user()->image }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block fw-medium" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="page-header-user-dropdown">
                    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom mb-1">
                        <img class="rounded-circle" src="{{ auth()->user()->image }}" alt="" style="width:32px;height:32px;object-fit:cover;">
                        <div style="min-width:0;">
                            <div class="fw-semibold small text-truncate">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size:.7rem;">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile') }}">
                        <i class="ri-user-line align-middle me-1"></i> {{ __('Profile') }}
                    </a>
                    <div class="d-md-none px-3 py-1 border-top mt-1">
                        @include('common.partials.role-switcher')
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                        <i class="ri-shut-down-line align-middle me-1"></i> {{ __('Logout') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
