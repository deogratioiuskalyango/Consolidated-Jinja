@foreach ($users as $user)
    @php
        $isOnline = isset($user->is_online) && $user->is_online;
        $lastMsg  = $user->last_message ?? '';
        $lastTime = $user->last_message_time ?? null;
        $unread   = $user->unseen_message_count ?? 0;
    @endphp
    <div class="chat-user-item"
         data-id="{{ $user->id }}"
         data-name="{{ $user->first_name }} {{ $user->last_name }}"
         data-online="{{ $isOnline ? '1' : '0' }}">
        <div class="chat-user-avatar">
            <img src="{{ $user->image }}" alt="{{ $user->first_name }}">
            @if($isOnline)
                <span class="online-dot"></span>
            @endif
        </div>
        <div class="chat-user-info">
            <div class="chat-user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
            <div class="chat-user-last user-last-message-{{ $user->id }}">
                @if(strlen($lastMsg) > 28)
                    {{ mb_substr($lastMsg, 0, 28) }}…
                @else
                    {{ $lastMsg }}
                @endif
            </div>
        </div>
        <div class="chat-user-meta">
            <span class="chat-user-time user-last-seen-time-{{ $user->id }}">
                @if($lastTime)
                    {{ \Carbon\Carbon::parse($lastTime)->diffForHumans(null, true, true) }}
                @endif
            </span>
            <span class="chat-unread-badge" data-badge="{{ $user->id }}"
                  style="{{ $unread > 0 ? '' : 'display:none' }}">{{ $unread }}</span>
        </div>
    </div>
@endforeach
