{{-- This partial is no longer used for rendering; header is built in JS. --}}
{{-- Kept for backward compat if referenced elsewhere. --}}
<div class="chat-header-avatar">
    <img src="{{ $user->image ?? asset('assets/images/no-image.jpg') }}"
         alt="{{ ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') }}">
    @if(!empty($user->is_online))
        <span class="online-dot"></span>
    @endif
</div>
<div class="chat-header-info">
    <div class="chat-header-name">{{ ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') }}</div>
    <div class="chat-header-status {{ !empty($user->is_online) ? 'online' : '' }}" id="peer-status">
        {{ !empty($user->is_online) ? __('Online') : __('Offline') }}
    </div>
</div>
