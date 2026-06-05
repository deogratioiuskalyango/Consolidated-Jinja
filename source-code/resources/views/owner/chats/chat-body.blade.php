@php
    $currentUserId = auth()->id();
    $prevDate = null;
@endphp

@foreach ($chats as $msg)
    @php
        $isMine     = $msg['sender_id'] == $currentUserId;
        $side       = $isMine ? 'mine' : 'theirs';
        $msgDate    = \Carbon\Carbon::parse($msg['created_at'])->format('Y-m-d');
        $isDeleted  = $msg['deleted_for_everyone'] ?? false;
        $reactions  = $msg['reactions'] ?? [];
        $replyTo    = $msg['reply_to'] ?? null;
    @endphp

    {{-- Date separator --}}
    @if($msgDate !== $prevDate)
        <div class="chat-date-sep">
            {{ \Carbon\Carbon::parse($msg['created_at'])->isToday() ? __('Today')
                : (\Carbon\Carbon::parse($msg['created_at'])->isYesterday() ? __('Yesterday')
                : \Carbon\Carbon::parse($msg['created_at'])->format('M d, Y')) }}
        </div>
        @php $prevDate = $msgDate; @endphp
    @endif

    <div class="chat-msg-row {{ $side }}" data-msg-id="{{ $msg['id'] }}" data-sender="{{ $msg['sender_id'] }}">

        {{-- Actions menu --}}
        @if(!$isDeleted)
        <div class="msg-actions">
            <button class="msg-action-btn" title="{{ __('Reply') }}"
                    data-action="reply"
                    data-msg-id="{{ $msg['id'] }}"
                    data-msg-text="{{ $msg['message_type'] == 1 ? Str::limit($msg['message'], 60) : '📎 Attachment' }}">
                <i class="ri-reply-line"></i>
            </button>
            <button class="msg-action-btn" title="{{ __('React') }}"
                    data-action="show-react"
                    data-msg-id="{{ $msg['id'] }}">
                <i class="ri-emotion-line"></i>
            </button>
            @if($isMine)
            <button class="msg-action-btn text-danger" title="{{ __('Delete') }}"
                    data-action="delete"
                    data-msg-id="{{ $msg['id'] }}">
                <i class="ri-delete-bin-line"></i>
            </button>
            @endif
        </div>

        {{-- Reaction picker --}}
        <div class="reaction-picker" id="rp-{{ $msg['id'] }}">
            @foreach(['👍','❤️','😂','😮','😢','🔥'] as $emoji)
                <button class="r-btn" data-react-msg="{{ $msg['id'] }}" data-emoji="{{ $emoji }}">{{ $emoji }}</button>
            @endforeach
        </div>
        @endif

        {{-- Bubble --}}
        <div class="chat-bubble {{ $isDeleted ? 'deleted' : '' }}">

            @if($isDeleted)
                <span class="fst-italic">🚫 {{ __('Message deleted') }}</span>
            @else
                {{-- Reply preview --}}
                @if($replyTo)
                    <div class="msg-reply-preview">
                        {{ $replyTo['message_type'] == 1 ? Str::limit($replyTo['message'], 60) : '📎 Attachment' }}
                    </div>
                @endif

                {{-- Content by type --}}
                @if($msg['message_type'] == 2 && $msg['file_url'])
                    <img src="{{ $msg['file_url'] }}" class="chat-img"
                         data-lightbox="{{ $msg['file_url'] }}" alt="image">

                @elseif($msg['message_type'] == 4 && $msg['file_url'])
                    <audio controls class="chat-audio" src="{{ $msg['file_url'] }}"></audio>

                @elseif($msg['message_type'] == 5 && $msg['file_url'])
                    <video controls class="chat-video" src="{{ $msg['file_url'] }}"></video>

                @elseif($msg['message_type'] == 3 && $msg['file_url'])
                    <a href="{{ $msg['file_url'] }}" target="_blank"
                       class="text-decoration-none {{ $isMine ? 'text-white' : '' }}">
                        <div class="chat-file-card">
                            <div class="chat-file-icon"><i class="{{ $msg['file_icon'] ?? 'ri-file-line' }}"></i></div>
                            <div class="chat-file-info">
                                <div class="chat-file-name">{{ $msg['file_name'] ?? 'File' }}</div>
                                <div class="chat-file-size">{{ $msg['file_size'] ?? '' }}</div>
                            </div>
                            <i class="ri-download-line chat-file-dl"></i>
                        </div>
                    </a>
                @endif

                @if($msg['message'])
                    <p class="mb-0">{!! nl2br(e($msg['message'])) !!}</p>
                @endif
            @endif
        </div>

        {{-- Footer: time + ticks --}}
        <div class="bubble-footer">
            <span>{{ $msg['time'] ?? '' }}</span>
            @if($isMine)
                <i class="ri-check-double-line tick-icon {{ $msg['is_seen'] ? 'seen' : '' }}"
                   data-tick="{{ $msg['id'] }}"></i>
            @endif
        </div>

        {{-- Reactions --}}
        <div class="msg-reactions" data-reactions="{{ $msg['id'] }}">
            @foreach($reactions as $emoji => $userIds)
                @php $mine = in_array($currentUserId, $userIds) ? 'mine' : ''; @endphp
                <span class="reaction-chip {{ $mine }}"
                      data-react-msg="{{ $msg['id'] }}"
                      data-emoji="{{ $emoji }}">{{ $emoji }} {{ count($userIds) }}</span>
            @endforeach
        </div>
    </div>
@endforeach
