@php
    $bnItems = array_slice($navItems ?? [], 0, 4);
    // First 2 items go left of hamburger, rest go right
    $bnLeft  = array_slice($bnItems, 0, 2);
    $bnRight = array_slice($bnItems, 2);
@endphp

{{-- Hidden on desktop (d-lg-none), fixed to viewport bottom on mobile --}}
<nav class="sh-bottom-nav d-lg-none" id="sh-bottom-nav" aria-label="{{ __('Mobile navigation') }}">

    @foreach($bnLeft as $item)
    @php $isActive = rtrim(request()->url(), '/') === rtrim($item['url'], '/'); @endphp
    <a href="{{ $item['url'] }}" class="sh-bottom-nav-item {{ $isActive ? 'active' : '' }}">
        <i class="{{ $item['icon'] ?? 'ri-arrow-right-line' }}"></i>
        <span>{{ __($item['label']) }}</span>
    </a>
    @endforeach

    {{-- Centre hamburger --}}
    <button type="button" class="sh-bottom-nav-item sh-bottom-nav-menu" id="sh-bottom-menu-btn" aria-label="{{ __('Open menu') }}">
        <i class="ri-menu-line" id="sh-bottom-menu-icon"></i>
        <span>{{ __('Menu') }}</span>
    </button>

    @foreach($bnRight as $item)
    @php $isActive = rtrim(request()->url(), '/') === rtrim($item['url'], '/'); @endphp
    <a href="{{ $item['url'] }}" class="sh-bottom-nav-item {{ $isActive ? 'active' : '' }}">
        <i class="{{ $item['icon'] ?? 'ri-arrow-right-line' }}"></i>
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

    document.addEventListener('click', function(){
        setTimeout(function(){
            if (!document.body.classList.contains('sidebar-enable') && icon) {
                icon.className = 'ri-menu-line';
            }
        }, 60);
    });
})();
</script>
