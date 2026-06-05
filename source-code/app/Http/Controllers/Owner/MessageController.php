<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\ChatService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class MessageController extends Controller
{
    use ResponseTrait;

    public ChatService $chatService;

    public function __construct()
    {
        $this->chatService = new ChatService();
    }

    public function index()
    {
        $data['title']         = __('Message');
        $data['activeMessage'] = 'active';
        $data['users']         = $this->chatService->getChatUserList()->getData()->data;

        return view('owner.chats.message', $data);
    }

    public function send(Request $request)
    {
        return $this->chatService->store($request);
    }

    public function getSingleChat(Request $request)
    {
        $senderId   = auth()->id();
        $receiverId = (int) $request->receiver_id;

        $rawChats             = $this->chatService->getSingleUserChat($senderId, $receiverId);
        $data['chats']        = $rawChats; // already formatted as arrays
        $data['receiver_id']  = $receiverId;
        $unseenData           = collect($this->chatService->unseenUserMessage()->getData()->data);

        $response['unseen_user_message']  = $unseenData;
        $response['total_unseen_message'] = $unseenData->sum('unseen_message_count');
        $response['html']                 = View::make('owner.chats.chat-body', $data)->render();

        return $this->success($response);
    }

    // ── Enhanced endpoints ───────────────────────────────────────────────────

    /** Delta-poll: returns only messages newer than after_id */
    public function poll(Request $request)
    {
        $receiverId = (int) $request->receiver_id;
        $afterId    = (int) ($request->after_id ?? 0);

        if (!$receiverId) {
            return $this->error([], 'receiver_id required');
        }

        $result = $this->chatService->getNewMessages($receiverId, $afterId);

        // Also return typing status
        $result['is_typing'] = $this->chatService->getTypingStatus($receiverId);

        return $this->success($result);
    }

    /** Record that the current user is typing to receiver_id */
    public function typing(Request $request)
    {
        $receiverId = (int) $request->receiver_id;
        if ($receiverId) {
            $this->chatService->updateTyping($receiverId);
        }
        return response()->json(['ok' => true]);
    }

    /** Delete a message (for_everyone=1 for hard wipe) */
    public function deleteMessage(Request $request, int $id)
    {
        $forEveryone = (bool) ($request->for_everyone ?? false);
        $result      = $this->chatService->deleteMessage($id, $forEveryone);

        return $result['success']
            ? $this->success([], $result['message'])
            : $this->error([], $result['message']);
    }

    /** Toggle emoji reaction on a message */
    public function react(Request $request, int $id)
    {
        $emoji  = $request->emoji ?? '';
        $result = $this->chatService->reactToMessage($id, $emoji);

        return $result['success']
            ? $this->success(['reactions' => $result['reactions']])
            : $this->error([], $result['message']);
    }

    /** Refresh user list (for sidebar AJAX refresh) */
    public function userList()
    {
        $users = $this->chatService->getChatUserList()->getData()->data;
        $html  = View::make('owner.chats.chat-user-list', ['users' => $users])->render();

        return $this->success(['html' => $html]);
    }
}
