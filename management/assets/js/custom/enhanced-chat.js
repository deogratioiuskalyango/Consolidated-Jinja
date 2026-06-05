/**
 * Enhanced Chat — Zaiproty
 * Adaptive polling, typing, reactions, file/voice/image, reply-to, delete, emoji picker
 */
(function () {
    'use strict';

    // ── Config ───────────────────────────────────────────────────────────────
    const ROUTES      = window.CHAT_ROUTES || {};
    const CURRENT_UID = parseInt(window.CHAT_CURRENT_UID || 0, 10);
    const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const EMOJIS = [
        '😀','😂','😍','🥰','😎','🤔','😢','😡','👍','👎',
        '❤️','🔥','🎉','💯','👏','🙏','😅','🤣','😇','🤩',
        '😴','🤯','🥳','😤','💪','🫡','😏','🫠','😬','🥺',
    ];
    const QUICK_REACTIONS = ['👍','❤️','😂','😮','😢','🔥'];

    // ── State ────────────────────────────────────────────────────────────────
    let activeReceiverId  = null;
    let lastMessageId     = 0;
    let pollTimer         = null;
    let typingTimer       = null;
    let recordingTimer    = null;
    let mediaRecorder     = null;
    let audioChunks       = [];
    let replyToData       = null;
    let pendingFile       = null;
    let isRecording       = false;
    let isTabActive       = !document.hidden;
    let seenMessageIds    = new Set();

    // ── DOM refs ─────────────────────────────────────────────────────────────
    const $sidebar      = () => document.getElementById('chat-sidebar');
    const $messagesArea = () => document.getElementById('chat-messages');
    const $headerArea   = () => document.getElementById('chat-header');
    const $typingArea   = () => document.getElementById('typing-indicator');
    const $textInput    = () => document.getElementById('chat-text-input');
    const $replyBar     = () => document.getElementById('reply-bar');
    const $filePreview  = () => document.getElementById('file-preview-bar');
    const $emojiPicker  = () => document.getElementById('emoji-picker-wrap');
    const $lightbox     = () => document.getElementById('img-lightbox');
    const $lightboxImg  = () => document.getElementById('lightbox-img');
    const $recTimer     = () => document.getElementById('recording-timer');

    // ── Bootstrap ────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        buildEmojiPicker();
        bindSidebar();
        bindInput();
        bindSendForm();
        bindGlobalEvents();
        startVisibilityWatch();

        // Auto-open first user
        const first = document.querySelector('.chat-user-item[data-id]');
        if (first) openConversation(parseInt(first.dataset.id, 10));
    });

    // ── Sidebar ───────────────────────────────────────────────────────────────
    function bindSidebar() {
        document.addEventListener('click', e => {
            const item = e.target.closest('.chat-user-item[data-id]');
            if (!item) return;
            const uid = parseInt(item.dataset.id, 10);
            document.querySelectorAll('.chat-user-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            openConversation(uid);
        });

        // Search filter
        const searchInput = document.getElementById('chat-search');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const q = searchInput.value.toLowerCase();
                document.querySelectorAll('.chat-user-item').forEach(item => {
                    const name = item.dataset.name?.toLowerCase() || '';
                    item.style.display = name.includes(q) ? '' : 'none';
                });
            });
        }
    }

    function openConversation(receiverId) {
        if (activeReceiverId === receiverId) return;
        activeReceiverId = receiverId;
        lastMessageId    = 0;
        seenMessageIds   = new Set();
        clearPoll();
        resetReplyTo();
        resetFilePreview();

        // Show header
        const item = document.querySelector(`.chat-user-item[data-id="${receiverId}"]`);
        if (item && $headerArea()) {
            $headerArea().innerHTML = buildHeader(item);
        }

        // Clear messages
        if ($messagesArea()) $messagesArea().innerHTML = '';

        // Initial load via existing getSingleChat
        fetch(`${ROUTES.single_user_chat}?receiver_id=${receiverId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(r => r.json())
            .then(data => {
                if (data.status && $messagesArea()) {
                    // data.data.html comes from chat-body blade
                    // but we now render JSON directly — if html is present use it, else build from JSON
                    if (data.data && data.data.html) {
                        $messagesArea().innerHTML = data.data.html;
                        // extract last id from rendered messages
                        const msgs = $messagesArea().querySelectorAll('[data-msg-id]');
                        msgs.forEach(m => {
                            const id = parseInt(m.dataset.msgId, 10);
                            if (id > lastMessageId) lastMessageId = id;
                        });
                    }
                    updateUnreadBadge(receiverId, 0);
                    scrollToBottom(false);
                    schedulePoll();
                }
            })
            .catch(console.error);

        // Clear unread badge for this user
        updateUnreadBadge(receiverId, 0);
    }

    function buildHeader(item) {
        const name   = item.dataset.name || '';
        const img    = item.querySelector('img')?.src || '';
        const online = item.dataset.online === '1';
        return `
        <div class="d-flex align-items-center gap-3">
          <div class="chat-header-avatar">
            <img src="${esc(img)}" alt="${esc(name)}">
            ${online ? '<span class="online-dot"></span>' : ''}
          </div>
          <div class="chat-header-info">
            <div class="chat-header-name">${esc(name)}</div>
            <div class="chat-header-status ${online ? 'online' : ''}" id="peer-status">
              ${online ? 'Online' : 'Offline'}
            </div>
          </div>
        </div>`;
    }

    // ── Polling ───────────────────────────────────────────────────────────────
    function schedulePoll() {
        clearPoll();
        const delay = isTabActive ? 2000 : 10000;
        pollTimer = setTimeout(doPoll, delay);
    }

    function clearPoll() {
        if (pollTimer) { clearTimeout(pollTimer); pollTimer = null; }
    }

    function doPoll() {
        if (!activeReceiverId) return;
        fetch(`${ROUTES.poll}?receiver_id=${activeReceiverId}&after_id=${lastMessageId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(r => r.json())
            .then(data => {
                if (!data.status) return;
                const { messages = [], seen_ids = [], is_typing = false } = data.data;

                // Append new messages
                if (messages.length) {
                    appendMessages(messages);
                    messages.forEach(m => { if (m.id > lastMessageId) lastMessageId = m.id; });
                    scrollToBottom(true);
                }

                // Update double-ticks for sent messages
                seen_ids.forEach(id => {
                    if (!seenMessageIds.has(id)) {
                        seenMessageIds.add(id);
                        updateTick(id, true);
                    }
                });

                // Typing
                const ta = $typingArea();
                if (ta) ta.classList.toggle('active', is_typing);

                // Refresh sidebar user status
                refreshPeerStatus(is_typing);

                schedulePoll();
            })
            .catch(() => schedulePoll());
    }

    function appendMessages(messages) {
        const area = $messagesArea();
        if (!area) return;

        messages.forEach(msg => {
            // Skip if already rendered
            if (area.querySelector(`[data-msg-id="${msg.id}"]`)) return;
            const row = buildMessageRow(msg);
            area.insertAdjacentHTML('beforeend', row);
        });
    }

    // ── Message rendering ────────────────────────────────────────────────────
    function buildMessageRow(msg) {
        const isMine = msg.is_mine;
        const side   = isMine ? 'mine' : 'theirs';
        const isDeleted = msg.deleted_for_everyone;

        let bubbleContent = '';

        if (isDeleted) {
            bubbleContent = `<span class="text-muted fst-italic">🚫 Message deleted</span>`;
        } else {
            // Reply preview
            if (msg.reply_to) {
                const rt = msg.reply_to;
                const rtText = rt.message_type === 1 ? esc(rt.message) : '📎 Attachment';
                bubbleContent += `<div class="msg-reply-preview">${rtText}</div>`;
            }

            // Body
            if (msg.message_type === 2 && msg.file_url) {
                bubbleContent += `<img src="${esc(msg.file_url)}" class="chat-img" data-lightbox="${esc(msg.file_url)}" alt="image">`;
            } else if (msg.message_type === 4 && msg.file_url) {
                bubbleContent += `<audio controls class="chat-audio" src="${esc(msg.file_url)}"></audio>`;
            } else if (msg.message_type === 5 && msg.file_url) {
                bubbleContent += `<video controls class="chat-video" src="${esc(msg.file_url)}"></video>`;
            } else if (msg.message_type === 3 && msg.file_url) {
                bubbleContent += `
                <a href="${esc(msg.file_url)}" target="_blank" class="text-decoration-none ${isMine ? 'text-white' : ''}">
                  <div class="chat-file-card">
                    <div class="chat-file-icon"><i class="${esc(msg.file_icon || 'ri-file-line')}"></i></div>
                    <div class="chat-file-info">
                      <div class="chat-file-name">${esc(msg.file_name || 'File')}</div>
                      <div class="chat-file-size">${esc(msg.file_size || '')}</div>
                    </div>
                    <i class="ri-download-line chat-file-dl"></i>
                  </div>
                </a>`;
            }

            if (msg.message) {
                bubbleContent += `<p class="mb-0">${nl2br(esc(msg.message))}</p>`;
            }
        }

        // Ticks
        const tickClass = msg.is_seen ? 'seen' : '';
        const ticks = isMine
            ? `<i class="ri-check-double-line tick-icon ${tickClass}" data-tick="${msg.id}"></i>`
            : '';

        // Reactions
        const reactionsHtml = buildReactionsHtml(msg.reactions || {}, msg.id);

        // Actions menu
        const actionsHtml = !isDeleted ? buildActionsMenu(msg) : '';

        return `
        <div class="chat-msg-row ${side}" data-msg-id="${msg.id}" data-sender="${msg.sender_id}">
          ${actionsHtml}
          <div class="chat-bubble ${isDeleted ? 'deleted' : ''}">
            ${bubbleContent}
          </div>
          <div class="bubble-footer">
            <span>${esc(msg.time || '')}</span>
            ${ticks}
          </div>
          ${reactionsHtml}
        </div>`;
    }

    function buildReactionsHtml(reactions, msgId) {
        const entries = Object.entries(reactions);
        if (!entries.length) return `<div class="msg-reactions" data-reactions="${msgId}"></div>`;
        const chips = entries.map(([emoji, users]) => {
            const mine = users.includes(CURRENT_UID) ? 'mine' : '';
            return `<span class="reaction-chip ${mine}" data-react-msg="${msgId}" data-emoji="${emoji}">${emoji} ${users.length}</span>`;
        }).join('');
        return `<div class="msg-reactions" data-reactions="${msgId}">${chips}</div>`;
    }

    function buildActionsMenu(msg) {
        const isMine = msg.is_mine;
        return `
        <div class="msg-actions">
          <button class="msg-action-btn" title="Reply" data-action="reply" data-msg-id="${msg.id}" data-msg-text="${esc(msg.message || (msg.message_type !== 1 ? '📎 Attachment' : ''))}">
            <i class="ri-reply-line"></i>
          </button>
          <button class="msg-action-btn" title="React" data-action="show-react" data-msg-id="${msg.id}">
            <i class="ri-emotion-line"></i>
          </button>
          ${isMine ? `<button class="msg-action-btn text-danger" title="Delete" data-action="delete" data-msg-id="${msg.id}"><i class="ri-delete-bin-line"></i></button>` : ''}
        </div>
        <div class="reaction-picker" id="rp-${msg.id}">
          ${QUICK_REACTIONS.map(e => `<button class="r-btn" data-react-msg="${msg.id}" data-emoji="${e}">${e}</button>`).join('')}
        </div>`;
    }

    // ── Input & send ─────────────────────────────────────────────────────────
    function bindInput() {
        const input = $textInput();
        if (!input) return;

        input.addEventListener('input', () => {
            // Auto-resize
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';
            // Typing indicator
            sendTyping();
        });

        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Emoji btn
        const emojiBtn = document.getElementById('chat-emoji-btn');
        if (emojiBtn) {
            emojiBtn.addEventListener('click', e => {
                e.stopPropagation();
                $emojiPicker()?.classList.toggle('visible');
            });
        }

        // Attach btn
        const attachBtn = document.getElementById('chat-attach-btn');
        const fileInput = document.getElementById('chat-file-input');
        if (attachBtn && fileInput) {
            attachBtn.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', handleFileSelect);
        }

        // Voice btn
        const voiceBtn = document.getElementById('chat-voice-btn');
        if (voiceBtn) {
            voiceBtn.addEventListener('click', toggleRecording);
        }
    }

    function bindSendForm() {
        const sendBtn = document.getElementById('chat-send-btn');
        if (sendBtn) sendBtn.addEventListener('click', sendMessage);
    }

    function sendMessage() {
        if (!activeReceiverId) return;
        const input = $textInput();
        const text  = input?.value.trim();

        if (!text && !pendingFile) return;

        const formData = new FormData();
        formData.append('_token', CSRF);
        formData.append('receiver_id', activeReceiverId);
        if (text)        formData.append('message', text);
        if (pendingFile) formData.append('file', pendingFile);
        if (replyToData) formData.append('reply_to_id', replyToData.id);

        // Optimistic: show message immediately
        const optimistic = buildOptimisticRow(text, pendingFile);
        $messagesArea()?.insertAdjacentHTML('beforeend', optimistic);
        scrollToBottom(false);

        // Clear
        if (input) { input.value = ''; input.style.height = 'auto'; }
        resetReplyTo();
        resetFilePreview();

        fetch(ROUTES.send_message, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData,
        })
            .then(r => r.json())
            .then(data => {
                if (data.status && data.data?.chat) {
                    // Replace optimistic with real
                    const real = data.data.chat;
                    const opt  = $messagesArea()?.querySelector('.optimistic');
                    if (opt) opt.outerHTML = buildMessageRow(real);
                    if (real.id > lastMessageId) lastMessageId = real.id;
                }
            })
            .catch(console.error);
    }

    function buildOptimisticRow(text, file) {
        let body = '';
        if (file && file.type.startsWith('image/')) {
            const url = URL.createObjectURL(file);
            body += `<img src="${url}" class="chat-img" alt="image">`;
        } else if (file) {
            body += `<div class="chat-file-card"><div class="chat-file-icon"><i class="ri-file-line"></i></div><div class="chat-file-info"><div class="chat-file-name">${esc(file.name)}</div></div></div>`;
        }
        if (text) body += `<p class="mb-0">${nl2br(esc(text))}</p>`;
        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        return `
        <div class="chat-msg-row mine optimistic">
          <div class="chat-bubble">${body}</div>
          <div class="bubble-footer"><span>${now}</span><i class="ri-check-line tick-icon"></i></div>
        </div>`;
    }

    // ── File select ───────────────────────────────────────────────────────────
    function handleFileSelect(e) {
        const file = e.target.files[0];
        if (!file) return;
        pendingFile = file;

        const bar = $filePreview();
        if (!bar) return;
        bar.classList.add('visible');
        bar.querySelector('.file-preview-name').textContent = file.name;

        if (file.type.startsWith('image/')) {
            const thumb = bar.querySelector('.file-preview-thumb');
            if (thumb) {
                thumb.src = URL.createObjectURL(file);
                thumb.style.display = '';
            }
        } else {
            const thumb = bar.querySelector('.file-preview-thumb');
            if (thumb) thumb.style.display = 'none';
        }
    }

    function resetFilePreview() {
        pendingFile = null;
        const bar = $filePreview();
        if (bar) { bar.classList.remove('visible'); }
        const fi = document.getElementById('chat-file-input');
        if (fi) fi.value = '';
    }

    // ── Voice recording ───────────────────────────────────────────────────────
    function toggleRecording() {
        if (!isRecording) startRecording();
        else stopRecording();
    }

    function startRecording() {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                audioChunks = [];
                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
                mediaRecorder.onstop = () => {
                    const blob = new Blob(audioChunks, { type: 'audio/webm' });
                    const file = new File([blob], `voice-${Date.now()}.webm`, { type: 'audio/webm' });
                    pendingFile = file;
                    sendMessage();
                    stream.getTracks().forEach(t => t.stop());
                };
                mediaRecorder.start();
                isRecording = true;
                const btn = document.getElementById('chat-voice-btn');
                if (btn) btn.classList.add('recording');

                // Timer
                let sec = 0;
                recordingTimer = setInterval(() => {
                    sec++;
                    const t = $recTimer();
                    if (t) { t.classList.add('active'); t.textContent = `${Math.floor(sec/60).toString().padStart(2,'0')}:${(sec%60).toString().padStart(2,'0')}`; }
                    if (sec >= 120) stopRecording(); // 2-min limit
                }, 1000);
            })
            .catch(() => alert('Microphone access denied'));
    }

    function stopRecording() {
        if (mediaRecorder && isRecording) {
            mediaRecorder.stop();
            isRecording = false;
            const btn = document.getElementById('chat-voice-btn');
            if (btn) btn.classList.remove('recording');
            clearInterval(recordingTimer);
            const t = $recTimer();
            if (t) t.classList.remove('active');
        }
    }

    // ── Typing indicator ──────────────────────────────────────────────────────
    function sendTyping() {
        if (!activeReceiverId) return;
        if (typingTimer) clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {}, 2000);

        fetch(ROUTES.typing, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `_token=${encodeURIComponent(CSRF)}&receiver_id=${activeReceiverId}`,
        }).catch(() => {});
    }

    // ── Reply-to ──────────────────────────────────────────────────────────────
    function setReplyTo(id, text) {
        replyToData = { id };
        const bar = $replyBar();
        if (bar) {
            bar.classList.add('visible');
            const t = bar.querySelector('.reply-bar-text');
            if (t) t.textContent = text;
        }
        $textInput()?.focus();
    }

    function resetReplyTo() {
        replyToData = null;
        const bar = $replyBar();
        if (bar) bar.classList.remove('visible');
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    function deleteMessage(msgId) {
        const forEveryone = confirm('Delete for everyone? (Cancel = delete only for you)');
        fetch(`${ROUTES.delete_message}`.replace('__ID__', msgId), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `_token=${encodeURIComponent(CSRF)}&for_everyone=${forEveryone ? 1 : 0}`,
        })
            .then(r => r.json())
            .then(data => {
                if (!data.status) return;
                const row = document.querySelector(`[data-msg-id="${msgId}"]`);
                if (!row) return;
                if (forEveryone) {
                    const bubble = row.querySelector('.chat-bubble');
                    if (bubble) {
                        bubble.classList.add('deleted');
                        bubble.innerHTML = '<span class="text-muted fst-italic">🚫 Message deleted</span>';
                    }
                    row.querySelector('.msg-actions')?.remove();
                    row.querySelector('.reaction-picker')?.remove();
                } else {
                    row.remove();
                }
            })
            .catch(console.error);
    }

    // ── Reactions ─────────────────────────────────────────────────────────────
    function reactToMessage(msgId, emoji) {
        fetch(`${ROUTES.react}`.replace('__ID__', msgId), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `_token=${encodeURIComponent(CSRF)}&emoji=${encodeURIComponent(emoji)}`,
        })
            .then(r => r.json())
            .then(data => {
                if (!data.status) return;
                const container = document.querySelector(`[data-reactions="${msgId}"]`);
                if (container) {
                    const reactions = data.data.reactions || {};
                    container.innerHTML = buildReactionsInner(reactions, msgId);
                }
                // Hide picker
                document.getElementById(`rp-${msgId}`)?.classList.remove('visible');
            })
            .catch(console.error);
    }

    function buildReactionsInner(reactions, msgId) {
        return Object.entries(reactions).map(([emoji, users]) => {
            const mine = users.includes(CURRENT_UID) ? 'mine' : '';
            return `<span class="reaction-chip ${mine}" data-react-msg="${msgId}" data-emoji="${emoji}">${emoji} ${users.length}</span>`;
        }).join('');
    }

    // ── Global event delegation ───────────────────────────────────────────────
    function bindGlobalEvents() {
        document.addEventListener('click', e => {
            // Close emoji picker
            if (!e.target.closest('#emoji-picker-wrap') && !e.target.closest('#chat-emoji-btn')) {
                $emojiPicker()?.classList.remove('visible');
            }
            // Close all reaction pickers
            if (!e.target.closest('.reaction-picker') && !e.target.closest('[data-action="show-react"]')) {
                document.querySelectorAll('.reaction-picker').forEach(p => p.classList.remove('visible'));
            }

            // Emoji picker insert
            const emojiBtn = e.target.closest('.emoji-btn[data-emoji]');
            if (emojiBtn) {
                const input = $textInput();
                if (input) {
                    const pos = input.selectionStart || 0;
                    const val = input.value;
                    input.value = val.slice(0, pos) + emojiBtn.dataset.emoji + val.slice(pos);
                    input.focus();
                    input.selectionStart = input.selectionEnd = pos + emojiBtn.dataset.emoji.length;
                }
                $emojiPicker()?.classList.remove('visible');
                return;
            }

            // Message action buttons
            const actionBtn = e.target.closest('[data-action]');
            if (actionBtn) {
                const action = actionBtn.dataset.action;
                const msgId  = parseInt(actionBtn.dataset.msgId, 10);
                if (action === 'reply') {
                    setReplyTo(msgId, actionBtn.dataset.msgText || '');
                } else if (action === 'delete') {
                    deleteMessage(msgId);
                } else if (action === 'show-react') {
                    document.querySelectorAll('.reaction-picker').forEach(p => p.classList.remove('visible'));
                    document.getElementById(`rp-${msgId}`)?.classList.toggle('visible');
                }
                return;
            }

            // Quick reaction chips (from message row)
            const rBtn = e.target.closest('.r-btn[data-react-msg]');
            if (rBtn) {
                reactToMessage(parseInt(rBtn.dataset.reactMsg, 10), rBtn.dataset.emoji);
                return;
            }

            // Reaction chip toggle
            const chip = e.target.closest('.reaction-chip[data-react-msg]');
            if (chip) {
                reactToMessage(parseInt(chip.dataset.reactMsg, 10), chip.dataset.emoji);
                return;
            }

            // Image lightbox
            const img = e.target.closest('[data-lightbox]');
            if (img) {
                openLightbox(img.dataset.lightbox);
                return;
            }

            // Close lightbox
            if (e.target.closest('#lightbox-close') || e.target.id === 'img-lightbox') {
                closeLightbox();
                return;
            }
        });

        // Reply bar close
        const replyClose = document.getElementById('reply-bar-close');
        if (replyClose) replyClose.addEventListener('click', resetReplyTo);

        // File preview remove
        const fpRemove = document.getElementById('file-preview-remove');
        if (fpRemove) fpRemove.addEventListener('click', resetFilePreview);
    }

    function buildEmojiPicker() {
        const picker = $emojiPicker();
        if (!picker) return;
        picker.innerHTML = `<div class="emoji-grid">${EMOJIS.map(e =>
            `<button class="emoji-btn" data-emoji="${e}">${e}</button>`
        ).join('')}</div>`;
    }

    // ── Lightbox ──────────────────────────────────────────────────────────────
    function openLightbox(src) {
        const lb  = $lightbox();
        const img = $lightboxImg();
        if (lb && img) { img.src = src; lb.classList.add('visible'); }
    }
    function closeLightbox() {
        $lightbox()?.classList.remove('visible');
    }

    // ── Tick update ───────────────────────────────────────────────────────────
    function updateTick(msgId, seen) {
        const tick = document.querySelector(`[data-tick="${msgId}"]`);
        if (tick) { tick.className = `ri-check-double-line tick-icon ${seen ? 'seen' : ''}`; }
    }

    // ── Unread badges ─────────────────────────────────────────────────────────
    function updateUnreadBadge(userId, count) {
        const badge = document.querySelector(`.chat-unread-badge[data-badge="${userId}"]`);
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? '' : 'none';
        }
    }

    // ── Status display ────────────────────────────────────────────────────────
    function refreshPeerStatus(isTyping) {
        const el = document.getElementById('peer-status');
        if (!el) return;
        if (isTyping) {
            el.textContent = 'typing...';
            el.className   = 'chat-header-status online';
        } else {
            const item = document.querySelector(`.chat-user-item[data-id="${activeReceiverId}"]`);
            const online = item?.dataset.online === '1';
            el.textContent = online ? 'Online' : 'Offline';
            el.className   = `chat-header-status ${online ? 'online' : ''}`;
        }
    }

    // ── Tab visibility ────────────────────────────────────────────────────────
    function startVisibilityWatch() {
        document.addEventListener('visibilitychange', () => {
            isTabActive = !document.hidden;
            if (isTabActive && activeReceiverId) {
                clearPoll();
                schedulePoll();
            }
        });
    }

    // ── Scroll ────────────────────────────────────────────────────────────────
    function scrollToBottom(smooth) {
        const area = $messagesArea();
        if (!area) return;
        const atBottom = area.scrollHeight - area.scrollTop - area.clientHeight < 150;
        if (smooth && !atBottom) return; // Don't steal scroll if user scrolled up
        area.scrollTo({ top: area.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    function esc(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function nl2br(str) {
        return String(str).replace(/\n/g, '<br>');
    }
})();
