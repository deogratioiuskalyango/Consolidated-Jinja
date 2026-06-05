@extends('owner.layouts.app')
@push('title'){{ $title }}@endpush

@push('style')
<link rel="stylesheet" href="{{ asset('assets/css/enhanced-chat.css') }}">
@endpush

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="chat-wrapper">

                {{-- ── Sidebar ──────────────────────────────────────────── --}}
                <div class="chat-sidebar" id="chat-sidebar">
                    <div class="chat-sidebar-header">
                        <h5>{{ __('Messages') }}</h5>
                        <div class="chat-search-box">
                            <i class="ri-search-line search-icon"></i>
                            <input type="text" id="chat-search" placeholder="{{ __('Search…') }}">
                        </div>
                    </div>
                    <div class="chat-user-list" id="chat-user-list">
                        @include('owner.chats.chat-user-list')
                    </div>
                </div>

                {{-- ── Main panel ───────────────────────────────────────── --}}
                <div class="chat-main">

                    {{-- Header --}}
                    <div class="chat-header" id="chat-header">
                        <div class="chat-empty-state w-100 py-3">
                            <i class="ri-chat-3-line"></i>
                            <p>{{ __('Select a conversation') }}</p>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div class="chat-messages" id="chat-messages">
                        <div class="chat-empty-state">
                            <i class="ri-message-2-line"></i>
                            <p>{{ __('No messages yet. Say hello!') }}</p>
                        </div>
                    </div>

                    {{-- Typing indicator --}}
                    <div class="typing-indicator px-4" id="typing-indicator">
                        <div class="typing-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <span>{{ __('typing…') }}</span>
                    </div>

                    {{-- Footer --}}
                    <div class="chat-footer">

                        {{-- Reply bar --}}
                        <div class="reply-bar" id="reply-bar">
                            <i class="ri-reply-line text-primary"></i>
                            <span class="reply-bar-text"></span>
                            <span class="reply-bar-close" id="reply-bar-close">×</span>
                        </div>

                        {{-- File preview --}}
                        <div class="file-preview-bar" id="file-preview-bar">
                            <img src="" class="file-preview-thumb" alt="" style="display:none">
                            <i class="ri-attachment-line text-primary"></i>
                            <span class="file-preview-name flex-1"></span>
                            <span class="file-preview-remove" id="file-preview-remove">×</span>
                        </div>

                        {{-- Input row --}}
                        <div class="chat-input-row position-relative">
                            {{-- Emoji picker --}}
                            <div class="emoji-picker-wrap" id="emoji-picker-wrap"></div>

                            <button class="chat-emoji-btn" id="chat-emoji-btn" type="button" title="{{ __('Emoji') }}">
                                <i class="ri-emotion-line"></i>
                            </button>
                            <button class="chat-attach-btn" id="chat-attach-btn" type="button" title="{{ __('Attach file') }}">
                                <i class="ri-attachment-2"></i>
                            </button>
                            <input type="file" id="chat-file-input" accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" style="display:none">

                            <textarea
                                id="chat-text-input"
                                class="chat-text-input"
                                rows="1"
                                placeholder="{{ __('Type a message…') }}"
                            ></textarea>

                            <span class="recording-timer" id="recording-timer"></span>

                            <button class="chat-voice-btn" id="chat-voice-btn" type="button" title="{{ __('Voice message') }}">
                                <i class="ri-mic-line"></i>
                            </button>
                            <button class="chat-send-btn" id="chat-send-btn" type="button" title="{{ __('Send') }}">
                                <i class="ri-send-plane-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image lightbox --}}
<div class="img-lightbox" id="img-lightbox">
    <span class="lightbox-close" id="lightbox-close">×</span>
    <img id="lightbox-img" src="" alt="preview">
</div>

{{-- Hidden route vars --}}
<script>
    window.CHAT_ROUTES = {
        single_user_chat: "{{ route('owner.chats.single_user_chat') }}",
        send_message:     "{{ route('owner.chats.send_message') }}",
        poll:             "{{ route('owner.chats.poll') }}",
        typing:           "{{ route('owner.chats.typing') }}",
        delete_message:   "{{ url('owner/chats/delete/__ID__') }}",
        react:            "{{ url('owner/chats/react/__ID__') }}",
        user_list:        "{{ route('owner.chats.user_list') }}",
    };
    window.CHAT_CURRENT_UID = {{ auth()->id() }};
</script>
@endsection

@push('script')
<script src="{{ asset('assets/js/custom/enhanced-chat.js') }}"></script>
@endpush
