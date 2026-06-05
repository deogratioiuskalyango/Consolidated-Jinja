<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\GovernanceMeeting;
use App\Models\MeetingAttendee;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        $shareholder = auth()->user()->shareholder;
        $ownerUserId = $shareholder->owner_user_id;
        $data['pageTitle']   = __('Meetings');
        $data['shareholder'] = $shareholder;
        $data['meetings'] = GovernanceMeeting::where('owner_user_id', $ownerUserId)
            ->with(['attendees' => fn($q) => $q->where('shareholder_id', $shareholder->id)])
            ->orderByRaw('status = ? DESC', [MEETING_STATUS_SCHEDULED])
            ->orderBy('scheduled_at')
            ->paginate(20);
        return view('shareholder.meetings.index', $data);
    }

    public function show(GovernanceMeeting $meeting)
    {
        $shareholder = auth()->user()->shareholder;
        abort_if($meeting->owner_user_id !== $shareholder->owner_user_id, 403);
        $data['pageTitle']    = $meeting->title;
        $data['meeting']      = $meeting->load('resolutions', 'documents.file', 'attendees.shareholder.user');
        $data['myAttendance'] = $meeting->attendees->where('shareholder_id', $shareholder->id)->first();
        $data['shareholder']  = $shareholder;
        return view('shareholder.meetings.show', $data);
    }

    public function confirm(Request $request, GovernanceMeeting $meeting)
    {
        $request->validate([
            'confirmed'       => 'required|boolean',
            'attendance_mode' => 'nullable|integer|in:1,2',
            'apology_note'    => 'nullable|string|max:500',
        ]);
        $shareholder = auth()->user()->shareholder;
        abort_if($meeting->owner_user_id !== $shareholder->owner_user_id, 403);
        abort_if($meeting->status !== MEETING_STATUS_SCHEDULED, 422);

        MeetingAttendee::updateOrCreate(
            ['meeting_id' => $meeting->id, 'shareholder_id' => $shareholder->id],
            [
                'confirmed'       => (bool) $request->confirmed,
                'attendance_mode' => $request->attendance_mode,
                'apology_note'    => $request->apology_note,
                'confirmed_at'    => now(),
            ]
        );

        return redirect()
            ->route('shareholder.meetings.show', $meeting)
            ->with('success', $request->confirmed ? __('Attendance confirmed.') : __('Apology submitted.'));
    }
}
