<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class ChatService
{
    use ResponseTrait;

    // Allowed reaction emojis
    const REACTIONS = ['👍', '❤️', '😂', '😮', '😢', '🔥'];

    // ── Store message ────────────────────────────────────────────────────────

    public function store($request)
    {
        $validated = $request->validate([
            'message'     => 'nullable|string|max:5000',
            'receiver_id' => 'required|exists:users,id',
            'file'        => 'nullable|file|max:51200',   // 50 MB
            'reply_to_id' => 'nullable|exists:chats,id',
        ]);

        if (empty($request->message) && !$request->hasFile('file')) {
            return $this->error([], __('Message or file is required'));
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();

            $data = [
                'sender_id'    => $user->id,
                'receiver_id'  => $request->receiver_id,
                'message'      => $request->message ? htmlspecialchars($request->message) : '',
                'message_type' => Chat::TYPE_TEXT,
                'reply_to_id'  => $request->reply_to_id ?? null,
            ];

            if ($request->hasFile('file')) {
                $file     = $request->file('file');
                $mime     = $file->getMimeType() ?? '';
                $origName = $file->getClientOriginalName();
                $size     = $file->getSize();

                // Determine message type from MIME
                if (str_starts_with($mime, 'image/')) {
                    $type = Chat::TYPE_IMAGE;
                } elseif (str_starts_with($mime, 'audio/')) {
                    $type = Chat::TYPE_AUDIO;
                } elseif (str_starts_with($mime, 'video/')) {
                    $type = Chat::TYPE_VIDEO;
                } else {
                    $type = Chat::TYPE_FILE;
                }

                $path = $file->store('files/Chat', 'public');

                $data['message_type'] = $type;
                $data['file_path']    = $path;
                $data['file_name']    = $origName;
                $data['file_size']    = $size;
                $data['file_mime']    = $mime;
            }

            $chat = Chat::create($data);

            // Update sender last_seen
            $user->update(['last_seen' => now()]);

            DB::commit();

            return $this->success(
                ['chat' => $this->formatMessage($chat->load('sender', 'replyTo'))],
                __('Send Successfully')
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error([], getErrorMessage($e, $e->getMessage()));
        }
    }

    // ── Load full conversation ───────────────────────────────────────────────

    public function getSingleUserChat(int $senderId, int $receiverId)
    {
        // Mark as seen — only messages sent TO me by this user
        Chat::between($senderId, $receiverId)
            ->where('sender_id', $receiverId)
            ->where('receiver_id', $senderId)
            ->where('is_seen', 0)
            ->update(['is_seen' => 1, 'is_delivered' => 1]);

        $chats = Chat::between($senderId, $receiverId)
            ->visibleFor($senderId)
            ->with(['sender', 'replyTo'])
            ->orderBy('created_at')
            ->get()
            ->map(fn($c) => $this->formatMessage($c));

        // Update my last_seen
        auth()->user()->update(['last_seen' => now()]);

        return $chats;
    }

    // ── Delta polling ────────────────────────────────────────────────────────

    public function getNewMessages(int $receiverId, int $afterId): array
    {
        $me = auth()->id();

        $messages = Chat::between($me, $receiverId)
            ->visibleFor($me)
            ->where('id', '>', $afterId)
            ->with(['sender', 'replyTo'])
            ->orderBy('created_at')
            ->get();

        // Mark newly received messages as seen + delivered
        $incoming = $messages->where('sender_id', $receiverId)->where('is_seen', 0);
        if ($incoming->isNotEmpty()) {
            Chat::whereIn('id', $incoming->pluck('id'))
                ->update(['is_seen' => 1, 'is_delivered' => 1]);
        }

        // IDs the sender can use to show double-ticks
        $seenIds = Chat::between($me, $receiverId)
            ->where('sender_id', $me)
            ->where('is_seen', 1)
            ->where('id', '>', max(0, $afterId - 100))
            ->pluck('id')
            ->toArray();

        // Update my last_seen
        auth()->user()->update(['last_seen' => now()]);

        return [
            'messages' => $messages->map(fn($c) => $this->formatMessage($c))->values()->toArray(),
            'seen_ids' => $seenIds,
        ];
    }

    // ── Typing indicators ────────────────────────────────────────────────────

    public function updateTyping(int $receiverId): void
    {
        DB::table('chat_typing')->upsert(
            [
                'user_id'     => auth()->id(),
                'receiver_id' => $receiverId,
                'updated_at'  => now(),
            ],
            ['user_id', 'receiver_id'],
            ['updated_at']
        );
    }

    public function getTypingStatus(int $fromUserId): bool
    {
        return DB::table('chat_typing')
            ->where('user_id', $fromUserId)
            ->where('receiver_id', auth()->id())
            ->where('updated_at', '>=', now()->subSeconds(4))
            ->exists();
    }

    // ── Delete message ───────────────────────────────────────────────────────

    public function deleteMessage(int $chatId, bool $forEveryone): array
    {
        $me   = auth()->id();
        $chat = Chat::find($chatId);

        if (!$chat) {
            return ['success' => false, 'message' => __('Message not found')];
        }

        if ($chat->sender_id !== $me && $chat->receiver_id !== $me) {
            return ['success' => false, 'message' => __('Unauthorized')];
        }

        if ($forEveryone && $chat->sender_id === $me) {
            $chat->update([
                'deleted_for_sender'   => true,
                'deleted_for_receiver' => true,
                'message'              => '',
            ]);
            // Remove file from storage
            if ($chat->file_path) {
                Storage::disk('public')->delete($chat->file_path);
            }
        } else {
            // Delete only for me
            if ($chat->sender_id === $me) {
                $chat->update(['deleted_for_sender' => true]);
            } else {
                $chat->update(['deleted_for_receiver' => true]);
            }
        }

        return ['success' => true, 'message' => __('Message deleted')];
    }

    // ── Reactions ────────────────────────────────────────────────────────────

    public function reactToMessage(int $chatId, string $emoji): array
    {
        if (!in_array($emoji, self::REACTIONS)) {
            return ['success' => false, 'message' => __('Invalid emoji')];
        }

        $me   = auth()->id();
        $chat = Chat::find($chatId);

        if (!$chat) {
            return ['success' => false, 'message' => __('Message not found')];
        }

        $reactions = $chat->reactions ?? [];

        if (!isset($reactions[$emoji])) {
            $reactions[$emoji] = [];
        }

        $key = array_search($me, $reactions[$emoji]);
        if ($key !== false) {
            // Toggle off
            array_splice($reactions[$emoji], $key, 1);
            if (empty($reactions[$emoji])) {
                unset($reactions[$emoji]);
            }
        } else {
            // Remove user from any other emoji first (one reaction per user)
            foreach ($reactions as $e => &$users) {
                $k = array_search($me, $users);
                if ($k !== false) {
                    array_splice($users, $k, 1);
                    if (empty($users)) unset($reactions[$e]);
                }
            }
            unset($users);
            $reactions[$emoji][] = $me;
        }

        $chat->update(['reactions' => $reactions]);

        return ['success' => true, 'reactions' => $reactions];
    }

    // ── User lists ───────────────────────────────────────────────────────────

    public function getChatUserList()
    {
        $me = auth()->id();

        $users = User::select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.last_seen',
                'file_managers.folder_name',
                'file_managers.file_name',
                DB::raw('(
                    SELECT message FROM chats
                    WHERE deleted_at IS NULL
                      AND ((sender_id = users.id AND receiver_id = ' . $me . ')
                        OR (receiver_id = users.id AND sender_id = ' . $me . '))
                    ORDER BY id DESC LIMIT 1
                ) AS last_message'),
                DB::raw('(
                    SELECT created_at FROM chats
                    WHERE deleted_at IS NULL
                      AND ((sender_id = users.id AND receiver_id = ' . $me . ')
                        OR (receiver_id = users.id AND sender_id = ' . $me . '))
                    ORDER BY id DESC LIMIT 1
                ) AS last_message_time')
            )
            ->leftJoin('file_managers', function ($join) {
                $join->on('file_managers.origin_id', '=', 'users.id')
                    ->where('file_managers.origin_type', '=', User::class);
            })
            ->where('users.role', USER_ROLE_TENANT)
            ->where('users.owner_user_id', $me)
            ->where('users.id', '!=', $me)
            ->where('users.status', 1)
            ->withCount(['unseen_message'])
            // ORDER BY the alias after get() — withCount() adds a GROUP BY which causes
            // MySQL error 1247 when ordering by a subquery alias inside the same query.
            ->get()
            ->sortByDesc('last_message_time')
            ->values()
            ->map(fn($user) => $this->mapUserForSidebar($user));

        return $this->success($users);
    }

    public function getChatUserListForTenant()
    {
        $me = auth()->id();

        // Find the owner of this tenant
        $ownerUserId = DB::table('users')->where('id', $me)->value('owner_user_id');

        // Tenant chats with: the owner + all other tenants under the same owner
        $users = User::select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.last_seen',
                'file_managers.folder_name',
                'file_managers.file_name',
                DB::raw('(
                    SELECT message FROM chats
                    WHERE deleted_at IS NULL
                      AND ((sender_id = users.id AND receiver_id = ' . $me . ')
                        OR (receiver_id = users.id AND sender_id = ' . $me . '))
                    ORDER BY id DESC LIMIT 1
                ) AS last_message'),
                DB::raw('(
                    SELECT created_at FROM chats
                    WHERE deleted_at IS NULL
                      AND ((sender_id = users.id AND receiver_id = ' . $me . ')
                        OR (receiver_id = users.id AND sender_id = ' . $me . '))
                    ORDER BY id DESC LIMIT 1
                ) AS last_message_time')
            )
            ->leftJoin('file_managers', function ($join) {
                $join->on('file_managers.origin_id', '=', 'users.id')
                    ->where('file_managers.origin_type', '=', User::class);
            })
            ->where(function ($q) use ($me, $ownerUserId) {
                // The owner of this tenant
                $q->where('users.id', $ownerUserId);
                // OR other active tenants under the same owner (optional: allow peer chat)
                // ->orWhere(function ($q2) use ($me, $ownerUserId) {
                //     $q2->where('users.owner_user_id', $ownerUserId)
                //        ->where('users.id', '!=', $me)
                //        ->where('users.role', USER_ROLE_TENANT);
                // });
            })
            ->where('users.id', '!=', $me)
            ->where('users.status', 1)
            ->withCount(['unseen_message'])
            // ORDER BY the alias after get() — withCount() adds a GROUP BY which causes
            // MySQL error 1247 when ordering by a subquery alias inside the same query.
            ->get()
            ->sortByDesc('last_message_time')
            ->values()
            ->map(fn($user) => $this->mapUserForSidebar($user));

        return $this->success($users);
    }

    private function mapUserForSidebar($user)
    {
        $user->image     = ($user->folder_name && $user->file_name)
            ? asset('storage/' . $user->folder_name . '/' . $user->file_name)
            : asset('assets/images/no-image.jpg');
        $user->is_online = $user->last_seen
            && \Carbon\Carbon::parse($user->last_seen)->diffInMinutes(now()) < 5;
        return $user;
    }

    public function unseenUserMessage()
    {
        $me    = auth()->id();
        $users = User::select(
                'users.first_name',
                'users.last_name',
                'users.id',
                'users.last_seen',
                DB::raw('(
                    SELECT created_at FROM chats
                    WHERE sender_id = users.id AND receiver_id = ' . $me . '
                      AND is_seen = 0 AND deleted_at IS NULL
                    ORDER BY id DESC LIMIT 1
                ) AS last_message_time'),
                DB::raw('(
                    SELECT message FROM chats
                    WHERE sender_id = users.id AND receiver_id = ' . $me . '
                      AND is_seen = 0 AND deleted_at IS NULL
                    ORDER BY id DESC LIMIT 1
                ) AS last_message')
            )
            ->where('users.role', USER_ROLE_TENANT)
            ->where('users.owner_user_id', $me)
            ->where('users.id', '!=', $me)
            ->withCount(['unseen_message'])
            ->get();

        return $this->success($users);
    }

    // ── Format helpers ───────────────────────────────────────────────────────

    private function formatMessage(Chat $chat): array
    {
        $me = auth()->id();
        return [
            'id'           => $chat->id,
            'sender_id'    => $chat->sender_id,
            'receiver_id'  => $chat->receiver_id,
            'message'      => $chat->message,
            'message_type' => $chat->message_type,
            'file_url'     => $chat->fileUrl(),
            'file_name'    => $chat->file_name,
            'file_size'    => $chat->humanFileSize(),
            'file_mime'    => $chat->file_mime,
            'file_icon'    => $chat->fileIconClass(),
            'reply_to'     => $chat->replyTo ? [
                'id'           => $chat->replyTo->id,
                'message'      => $chat->replyTo->message,
                'message_type' => $chat->replyTo->message_type,
                'sender_id'    => $chat->replyTo->sender_id,
            ] : null,
            'reactions'    => $chat->reactions ?? [],
            'is_seen'      => (bool) $chat->is_seen,
            'is_delivered' => (bool) $chat->is_delivered,
            'is_mine'      => $chat->sender_id === $me,
            'deleted_for_everyone' => $chat->deleted_for_sender && $chat->deleted_for_receiver,
            'sender_name'  => $chat->sender ? $chat->sender->first_name . ' ' . $chat->sender->last_name : '',
            'created_at'   => $chat->created_at?->toISOString(),
            'time'         => $chat->created_at?->format('h:i A'),
            'date'         => $chat->created_at?->format('Y-m-d'),
            'edited_at'    => $chat->edited_at?->toISOString(),
        ];
    }
}
