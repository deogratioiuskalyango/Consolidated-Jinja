@php
    $activeRoleSlug = session('active_role', null);
    $user = auth()->user();
    $primarySlug = \App\Models\SystemUserRole::primaryRoleSlugMap()[$user->role] ?? null;
    $additionalSlugs = \App\Models\SystemUserRole::where('user_id', $user->id)
        ->where('is_active', true)
        ->pluck('role_slug')
        ->toArray();
    $allSlugs = collect(array_unique(array_merge($primarySlug ? [$primarySlug] : [], $additionalSlugs)));
    $allSlugs = $allSlugs->filter(function($slug) use ($user) {
        if ($slug === 'shareholder') {
            return $user->shareholder && $user->shareholder->status == SHAREHOLDER_STATUS_ACTIVE;
        }
        if ($slug === 'accountant') {
            return $user->accountant && $user->accountant->status == ACCOUNTANT_STATUS_ACTIVE;
        }
        return true;
    })->values();
    $activeMeta = $activeRoleSlug ? \App\Models\SystemUserRole::getRoleMeta($activeRoleSlug) : null;
@endphp

@if($allSlugs->count() > 1)
<div class="dropdown d-inline-block">
    <button type="button"
            class="header-item noti-icon"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="{{ __('Switch Role') }}">
        <i class="{{ $activeMeta['icon'] ?? 'ri-swap-box-line' }}"></i>
        <span class="d-none d-xl-inline-block ms-1 font-medium" style="font-size: 12px;">
            {{ $activeMeta['label'] ?? __('Switch Role') }}
        </span>
    </button>

    <div class="dropdown-menu dropdown-menu-end" style="min-width: 220px;">
        <h6 class="dropdown-header">{{ __('Switch Dashboard') }}</h6>

        @foreach($allSlugs as $slug)
            @php $meta = \App\Models\SystemUserRole::getRoleMeta($slug); @endphp
            <form method="POST" action="{{ route('role.select') }}" class="d-inline w-100">
                @csrf
                <input type="hidden" name="role" value="{{ $slug }}">
                <button type="submit"
                        class="dropdown-item d-flex align-items-center {{ $activeRoleSlug == $slug ? 'active' : '' }}">
                    <i class="{{ $meta['icon'] }} me-2 text-{{ $meta['color'] }}"></i>
                    <span class="flex-grow-1">{{ $meta['label'] }}</span>
                    @if($activeRoleSlug == $slug)
                        <span class="badge bg-success ms-2" style="font-size: 10px;">{{ __('Active') }}</span>
                    @endif
                </button>
            </form>
        @endforeach

        <div class="dropdown-divider"></div>

        <a class="dropdown-item" href="{{ route('role.select.index') }}">
            <i class="ri-layout-grid-line me-2"></i>{{ __('All Dashboards') }}
        </a>
    </div>
</div>
@endif
