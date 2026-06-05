{{-- Overlay backdrop — tap outside closes sidebar on mobile --}}
<div class="sh-sidebar-overlay" id="sh-sidebar-overlay"></div>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                @foreach(($navItems ?? []) as $item)
                    @php $isActive = rtrim(request()->url(), '/') === rtrim($item['url'], '/') ? 'active mm-active' : ''; @endphp
                    <li class="{{ $isActive }}">
                        <a href="{{ $item['url'] }}" class="{{ $isActive }}">
                            <i class="{{ $item['icon'] ?? 'ri-arrow-right-line' }}"></i>
                            <span>{{ __($item['label']) }}</span>
                        </a>
                    </li>
                @endforeach

                <li>
                    <a href="{{ route('role.select.index') }}">
                        <i class="ri-swap-box-line"></i>
                        <span>{{ __('Switch Role') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('profile') }}">
                        <i class="ri-user-settings-line"></i>
                        <span>{{ __('Profile') }}</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
