<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\ShareholderNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $shareholder = auth()->user()->shareholder;
        $data['pageTitle']     = __('Notifications');
        $data['shareholder']   = $shareholder;
        $data['notifications'] = ShareholderNotification::where(function ($q) use ($shareholder) {
                $q->where('shareholder_id', $shareholder->id)
                  ->orWhere(function ($q2) use ($shareholder) {
                      $q2->where('owner_user_id', $shareholder->owner_user_id)
                         ->where('is_broadcast', true);
                  });
            })
            ->orderByDesc('created_at')
            ->paginate(30);
        return view('shareholder.notifications.index', $data);
    }

    public function markRead(Request $request, ShareholderNotification $notification)
    {
        $shareholder = auth()->user()->shareholder;
        abort_if($notification->shareholder_id && $notification->shareholder_id !== $shareholder->id, 403);
        $notification->update(['is_read' => true, 'read_at' => now()]);
        if ($request->expectsJson()) return response()->json(['success' => true]);
        return redirect()->back();
    }

    public function markAllRead(Request $request)
    {
        $shareholder = auth()->user()->shareholder;
        ShareholderNotification::where('shareholder_id', $shareholder->id)->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return redirect()->back()->with('success', __('All notifications marked as read.'));
    }
}
