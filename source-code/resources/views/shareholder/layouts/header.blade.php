@php
    $unreadCount = auth()->user()->shareholder?->notifications()->where('is_read', false)->count() ?? 0;
@endphp
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex align-items-center">
            <div class="navbar-brand-box">
                <a href="{{ route('shareholder.dashboard') }}" class="logo logo-light">
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

            {{-- Language Switcher — hidden on xs --}}
            <div class="dropdown d-none d-sm-inline-block">
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

            {{-- Notifications Bell --}}
            <div class="dropdown d-inline-block">
                <button type="button" class="header-item noti-icon" id="sh-notif-dropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-notification-2-fill"></i>
                    @if($unreadCount > 0)
                        <span class="noti-dot pulse"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0 sh-notif-menu"
                     aria-labelledby="sh-notif-dropdown">
                    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">{{ __('Notifications') }}</h6>
                        @if($unreadCount > 0)
                            <span class="badge bg-danger">{{ $unreadCount > 99 ? '99+' : $unreadCount }} {{ __('new') }}</span>
                        @endif
                    </div>
                    @php
                        $recentNotifs = auth()->user()->shareholder?->notifications()
                            ->orderByDesc('created_at')->limit(5)->get() ?? collect();
                    @endphp
                    @forelse($recentNotifs as $notif)
                        <div class="px-3 py-2 border-bottom d-flex align-items-start gap-2 {{ !$notif->is_read ? 'bg-light' : '' }}"
                             style="font-size:13px;">
                            <div class="flex-shrink-0 mt-1">
                                <i class="ri-notification-2-line {{ !$notif->is_read ? 'text-primary' : 'text-muted' }}"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold text-truncate">{{ $notif->title }}</div>
                                <div class="text-muted small text-truncate">{{ $notif->message }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                            @if(!$notif->is_read)
                                <span class="flex-shrink-0 mt-1" style="width:7px;height:7px;border-radius:50%;background:#dc3545;display:inline-block;"></span>
                            @endif
                        </div>
                    @empty
                        <div class="px-3 py-3 text-center text-muted small">
                            <i class="ri-notification-off-line d-block fs-4 mb-1"></i>
                            {{ __('No notifications') }}
                        </div>
                    @endforelse
                    <div class="px-3 py-2 text-center border-top">
                        <a href="{{ route('shareholder.notifications.index') }}" class="text-primary small fw-semibold">
                            {{ __('View All Notifications') }}
                        </a>
                    </div>
                </div>
            </div>

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
                <div class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="page-header-user-dropdown">
                    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom mb-1">
                        <img class="rounded-circle" src="{{ auth()->user()->image }}" alt="" style="width:32px;height:32px;object-fit:cover;">
                        <div style="min-width:0;">
                            <div class="fw-semibold small text-truncate">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size:.7rem;text-truncate;">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <a class="dropdown-item" href="{{ route('shareholder.profile') }}">
                        <i class="ri-user-line align-middle me-1"></i> {{ __('Profile') }}
                    </a>
                    {{-- Role switcher in dropdown on mobile --}}
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
