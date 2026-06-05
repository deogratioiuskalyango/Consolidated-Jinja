@php
    $unreadCount = auth()->user()->shareholder?->notifications()->where('is_read', false)->count() ?? 0;
    $bnItems = [
        ['route' => 'shareholder.dashboard',                 'icon' => 'ri-dashboard-line',       'label' => 'Home',      'active' => @$navDashboardActiveClass],
        ['route' => 'shareholder.resolutions.index',         'icon' => 'ri-survey-line',           'label' => 'Voting',    'active' => @$navResolutionsActiveClass],
        ['route' => 'shareholder.financial-approvals.index', 'icon' => 'ri-checkbox-circle-line',  'label' => 'Approvals', 'active' => @$navFinancialApprovalsActiveClass],
        ['route' => 'shareholder.meetings.index',            'icon' => 'ri-calendar-event-line',   'label' => 'Meetings',  'active' => @$navMeetingsActiveClass],
        ['route' => 'shareholder.notifications.index',       'icon' => 'ri-notification-2-line',   'label' => 'Alerts',    'active' => @$navNotificationsActiveClass, 'badge' => $unreadCount],
    ];
@endphp

{{-- Hidden on desktop (d-lg-none), fixed to viewport bottom on mobile --}}
<nav class="sh-bottom-nav d-lg-none" id="sh-bottom-nav" aria-label="{{ __('Mobile navigation') }}">

    @foreach(array_slice($bnItems, 0, 2) as $item)
    <a href="{{ route($item['route']) }}" class="sh-bottom-nav-item {{ $item['active'] ? 'active' : '' }}">
        @if(!empty($item['badge']) && $item['badge'] > 0)
            <span class="sh-bottom-nav-badge">{{ $item['badge'] > 9 ? '9+' : $item['badge'] }}</span>
        @endif
        <i class="{{ $item['icon'] }}"></i>
        <span>{{ __($item['label']) }}</span>
    </a>
    @endforeach

    {{-- Centre hamburger --}}
    <button type="button" class="sh-bottom-nav-item sh-bottom-nav-menu" id="sh-bottom-menu-btn" aria-label="{{ __('Open menu') }}">
        <i class="ri-menu-line" id="sh-bottom-menu-icon"></i>
        <span>{{ __('Menu') }}</span>
    </button>

    @foreach(array_slice($bnItems, 2) as $item)
    <a href="{{ route($item['route']) }}" class="sh-bottom-nav-item {{ $item['active'] ? 'active' : '' }}">
        @if(!empty($item['badge']) && $item['badge'] > 0)
            <span class="sh-bottom-nav-badge">{{ $item['badge'] > 9 ? '9+' : $item['badge'] }}</span>
        @endif
        <i class="{{ $item['icon'] }}"></i>
        <span>{{ __($item['label']) }}</span>
    </a>
    @endforeach

</nav>

<script>
(function() {
    var overlay  = document.getElementById('sh-sidebar-overlay');
    var btn      = document.getElementById('sh-bottom-menu-btn');
    var icon     = document.getElementById('sh-bottom-menu-icon');

    function openSidebar()  { document.body.classList.add('sidebar-enable');    if(icon){ icon.className='ri-close-line'; } }
    function closeSidebar() { document.body.classList.remove('sidebar-enable'); if(icon){ icon.className='ri-menu-line'; } }

    if (overlay) overlay.addEventListener('click', closeSidebar);
    if (btn) btn.addEventListener('click', function(e){
        e.stopPropagation();
        document.body.classList.contains('sidebar-enable') ? closeSidebar() : openSidebar();
    });

    // Sync icon when custom.js closes the sidebar
    document.addEventListener('click', function(){
        setTimeout(function(){
            if (!document.body.classList.contains('sidebar-enable') && icon) {
                icon.className = 'ri-menu-line';
            }
        }, 60);
    });
})();
</script>
