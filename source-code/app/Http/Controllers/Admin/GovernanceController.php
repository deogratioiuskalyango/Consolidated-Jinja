<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resolution;
use App\Models\FinancialApproval;
use App\Models\GovernanceMeeting;
use App\Models\GovernanceDocument;
use App\Models\DividendDeclaration;
use App\Models\Shareholder;
use App\Models\ShareholderNotification;
use App\Models\GovernanceAuditLog;
use App\Models\FileManager;
use App\Models\MeetingAttendee;
use App\Services\GoogleMeetService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GovernanceController extends Controller
{
    use ResponseTrait;

    // ---- Resolutions ----
    public function resolutionsIndex()
    {
        $data['pageTitle']   = __('Resolutions');
        $data['resolutions'] = Resolution::where('owner_user_id', auth()->id())
            ->with('votes')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.governance.resolutions.index', $data);
    }

    public function resolutionStore(Request $request)
    {
        $request->validate([
            'title'            => 'required|max:191',
            'type'             => 'required|integer|min:1|max:8',
            'description'      => 'required',
            'voting_opens_at'  => 'required|date',
            'voting_closes_at' => 'required|date|after:voting_opens_at',
            'quorum_percentage'=> 'nullable|numeric|min:0|max:100',
            'pass_threshold'   => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $count = Resolution::where('owner_user_id', auth()->id())->count() + 1;
            $resolution = Resolution::create([
                'owner_user_id'     => auth()->id(),
                'created_by'        => auth()->id(),
                'title'             => $request->title,
                'reference_number'  => 'RES-' . now()->format('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT),
                'type'              => $request->type,
                'description'       => $request->description,
                'supporting_details'=> $request->supporting_details,
                'status'            => $request->voting_opens_at <= now() ? RESOLUTION_STATUS_OPEN : RESOLUTION_STATUS_DRAFT,
                'anonymous_voting'  => (bool) $request->anonymous_voting,
                'weighted_voting'   => (bool) $request->get('weighted_voting', true),
                'quorum_percentage' => $request->quorum_percentage ?? 51,
                'pass_threshold'    => $request->pass_threshold ?? 51,
                'voting_opens_at'   => $request->voting_opens_at,
                'voting_closes_at'  => $request->voting_closes_at,
                'meeting_id'        => $request->meeting_id,
            ]);

            // Notify all shareholders
            ShareholderNotification::notifyAll(
                auth()->id(), 'new_resolution',
                'New Resolution: ' . $resolution->title,
                'A new resolution has been posted for voting. Voting closes: ' . \Carbon\Carbon::parse($request->voting_closes_at)->format('d M Y'),
                route('shareholder.resolutions.show', $resolution->id),
                $resolution
            );

            DB::commit();
            return $this->success([], __('Resolution created successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function resolutionEdit(Resolution $resolution)
    {
        abort_if($resolution->owner_user_id !== auth()->id(), 403);
        return response()->json([
            'status' => 200,
            'data'   => [
                'id'                => $resolution->id,
                'title'             => $resolution->title,
                'description'       => $resolution->description,
                'type'              => $resolution->type,
                'voting_opens_at'   => $resolution->voting_opens_at ? $resolution->voting_opens_at->format('Y-m-d\TH:i') : '',
                'voting_closes_at'  => $resolution->voting_closes_at ? $resolution->voting_closes_at->format('Y-m-d\TH:i') : '',
                'quorum_percentage' => $resolution->quorum_percentage,
                'pass_threshold'    => $resolution->pass_threshold,
                'weighted_voting'   => $resolution->weighted_voting,
                'anonymous_voting'  => $resolution->anonymous_voting,
                'has_votes'         => $resolution->votes()->exists(),
            ],
        ]);
    }

    public function resolutionUpdate(Request $request, Resolution $resolution)
    {
        abort_if($resolution->owner_user_id !== auth()->id(), 403);
        abort_if($resolution->votes()->exists(), 403, 'Cannot edit a resolution that already has votes.');

        $request->validate([
            'title'            => 'required|max:191',
            'type'             => 'required|integer|min:1|max:8',
            'description'      => 'required',
            'voting_opens_at'  => 'required|date',
            'voting_closes_at' => 'required|date|after:voting_opens_at',
            'quorum_percentage'=> 'nullable|numeric|min:0|max:100',
            'pass_threshold'   => 'nullable|numeric|min:0|max:100',
        ]);

        $resolution->update([
            'title'             => $request->title,
            'type'              => $request->type,
            'description'       => $request->description,
            'status'            => $request->voting_opens_at <= now() ? RESOLUTION_STATUS_OPEN : RESOLUTION_STATUS_DRAFT,
            'anonymous_voting'  => (bool) $request->anonymous_voting,
            'weighted_voting'   => (bool) $request->get('weighted_voting', true),
            'quorum_percentage' => $request->quorum_percentage ?? 51,
            'pass_threshold'    => $request->pass_threshold ?? 51,
            'voting_opens_at'   => $request->voting_opens_at,
            'voting_closes_at'  => $request->voting_closes_at,
        ]);

        GovernanceAuditLog::record('update_resolution', $resolution, [], 'Resolution updated by admin');
        return $this->success([], __('Resolution updated successfully.'));
    }

    public function resolutionDestroy(Resolution $resolution)
    {
        abort_if($resolution->owner_user_id !== auth()->id(), 403);
        abort_if($resolution->votes()->exists(), 403, 'Cannot delete a resolution that already has votes.');

        GovernanceAuditLog::record('delete_resolution', $resolution, [], 'Resolution deleted by admin');
        $resolution->delete();
        return $this->success([], __('Resolution deleted successfully.'));
    }

    // ---- Financial Approvals ----
    public function approvalsIndex()
    {
        $data['pageTitle']    = __('Financial Approvals');
        $data['approvals']    = FinancialApproval::where('owner_user_id', auth()->id())
            ->with('actions', 'requestedBy')
            ->orderByDesc('created_at')
            ->paginate(20);
        $data['shareholders'] = Shareholder::where('owner_user_id', auth()->id())
            ->where('status', SHAREHOLDER_STATUS_ACTIVE)
            ->with('user')
            ->get();
        return view('admin.governance.approvals.index', $data);
    }

    public function approvalStore(Request $request)
    {
        $request->validate([
            'title'       => 'required|max:191',
            'amount'      => 'required|numeric|min:0',
            'description' => 'required',
            'deadline_at' => 'nullable|date|after:now',
        ]);

        DB::beginTransaction();
        try {
            $count = FinancialApproval::where('owner_user_id', auth()->id())->count() + 1;
            $approval = FinancialApproval::create([
                'owner_user_id'      => auth()->id(),
                'requested_by'       => auth()->id(),
                'reference_number'   => 'FA-' . now()->format('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT),
                'title'              => $request->title,
                'department'         => $request->department,
                'amount'             => $request->amount,
                'currency'           => $request->currency ?? 'UGX',
                'payment_category'   => $request->payment_category,
                'priority'           => $request->priority ?? 2,
                'description'        => $request->description,
                'financial_impact'   => $request->financial_impact,
                'approval_threshold' => $request->approval_threshold ?? 51,
                'deadline_at'        => $request->deadline_at,
            ]);

            // Handle document upload
            if ($request->hasFile('document')) {
                $file = new FileManager();
                $upload = $file->upload('FinancialApproval', $request->document);
                if ($upload['status']) {
                    GovernanceDocument::create([
                        'owner_user_id'   => auth()->id(),
                        'uploaded_by'     => auth()->id(),
                        'title'           => 'Supporting Document - ' . $approval->title,
                        'document_type'   => 'financial_approval',
                        'file_id'         => $upload['file']->id,
                        'status'          => DOC_STATUS_ACTIVE,
                        'all_shareholders'=> false,
                    ]);
                }
            }

            ShareholderNotification::notifyAll(
                auth()->id(), 'approval_request',
                'Financial Approval Required: ' . $approval->title,
                'A financial expense of ' . number_format($approval->amount, 2) . ' ' . $approval->currency . ' requires your approval.',
                route('shareholder.financial-approvals.show', $approval->id),
                $approval
            );

            DB::commit();
            return $this->success([], __('Financial approval request created.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    // ---- Documents ----
    public function documentsIndex()
    {
        $data['pageTitle'] = __('Governance Documents');
        $data['documents'] = GovernanceDocument::where('owner_user_id', auth()->id())
            ->with('file', 'uploadedBy')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.governance.documents.index', $data);
    }

    public function documentStore(Request $request)
    {
        $request->validate([
            'title'         => 'required|max:191',
            'document_type' => 'required',
            'file'          => 'required|file|max:20480',
        ]);

        DB::beginTransaction();
        try {
            $fileManager = new FileManager();
            $upload = $fileManager->upload('GovernanceDocument', $request->file('file'));
            if (!$upload['status']) throw new \Exception($upload['message']);

            GovernanceDocument::create([
                'owner_user_id'     => auth()->id(),
                'uploaded_by'       => auth()->id(),
                'title'             => $request->title,
                'document_type'     => $request->document_type,
                'description'       => $request->description,
                'file_id'           => $upload['file']->id,
                'version'           => $request->version ?? '1.0',
                'status'            => DOC_STATUS_ACTIVE,
                'requires_signature'=> (bool) $request->requires_signature,
                'expiry_date'       => $request->expiry_date,
                'all_shareholders'  => (bool) $request->get('all_shareholders', true),
                'meeting_id'        => $request->meeting_id,
                'resolution_id'     => $request->resolution_id,
            ]);

            DB::commit();
            return $this->success([], __('Document uploaded successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function documentDelete(GovernanceDocument $document)
    {
        abort_if($document->owner_user_id !== auth()->id(), 403);
        DB::beginTransaction();
        try {
            if ($document->file) {
                $document->file->removeFile();
                $document->file->delete();
            }
            $document->delete();
            DB::commit();
            return $this->success([], __('Document deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    // ---- Dividends ----
    public function dividendsIndex()
    {
        $data['pageTitle']    = __('Dividend Management');
        $data['declarations'] = DividendDeclaration::where('owner_user_id', auth()->id())
            ->with('declaredBy', 'payments')
            ->orderByDesc('created_at')
            ->paginate(20);
        $data['shareClasses'] = \App\Models\ShareClass::where('status', ACTIVE)->get();
        return view('admin.governance.dividends.index', $data);
    }

    public function dividendStore(Request $request)
    {
        $request->validate([
            'title'           => 'required|max:191',
            'total_amount'    => 'required|numeric|min:0',
            'per_share_amount'=> 'required|numeric|min:0',
            'declaration_date'=> 'required|date',
            'payment_date'    => 'nullable|date|after_or_equal:declaration_date',
        ]);

        DB::beginTransaction();
        try {
            $count = DividendDeclaration::where('owner_user_id', auth()->id())->count() + 1;
            $declaration = DividendDeclaration::create([
                'owner_user_id'   => auth()->id(),
                'declared_by'     => auth()->id(),
                'reference_number'=> 'DIV-' . now()->format('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT),
                'title'           => $request->title,
                'total_amount'    => $request->total_amount,
                'per_share_amount'=> $request->per_share_amount,
                'currency'        => $request->currency ?? 'UGX',
                'status'          => DIVIDEND_STATUS_DECLARED,
                'declaration_date'=> $request->declaration_date,
                'payment_date'    => $request->payment_date,
                'notes'           => $request->notes,
            ]);

            $declaration->generatePayments();

            ShareholderNotification::notifyAll(
                auth()->id(), 'dividend',
                'Dividend Declared: ' . $declaration->title,
                'A dividend of ' . number_format($request->per_share_amount, 4) . ' per share has been declared.',
                route('shareholder.dividends.index'),
                $declaration
            );

            DB::commit();
            return $this->success([], __('Dividend declared and payments calculated.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    // ---- Meetings ----
    public function meetingsIndex()
    {
        $base = GovernanceMeeting::where('owner_user_id', auth()->id());

        $data['pageTitle']      = __('Governance Meetings');
        $data['meetings']       = (clone $base)->withCount('attendees')->orderByDesc('scheduled_at')->paginate(20);
        $data['totalCount']     = (clone $base)->count();
        $data['scheduledCount'] = (clone $base)->where('status', MEETING_STATUS_SCHEDULED)->count();
        $data['completedCount'] = (clone $base)->where('status', MEETING_STATUS_COMPLETED)->count();
        $data['cancelledCount'] = (clone $base)->where('status', MEETING_STATUS_CANCELLED)->count();
        $data['googleMeetReady'] = GoogleMeetService::isConfigured();
        return view('admin.governance.meetings.index', $data);
    }

    public function meetingStore(Request $request)
    {
        $request->validate([
            'title'                    => 'required|max:191',
            'type'                     => 'required|in:1,2,3,4',
            'mode'                     => 'required|in:1,2,3',
            'meeting_date'             => 'required|date_format:Y-m-d',
            'meeting_time'             => 'required|date_format:H:i',
            'chairman'                 => 'nullable|string|max:191',
            'recurrence_type'          => 'nullable|in:weekly,monthly',
            'recurrence_day_of_week'   => 'nullable|integer|between:0,6',
            'recurrence_day_of_month'  => 'nullable|integer|between:1,31',
            'recurrence_end_date'      => 'nullable|date',
        ]);

        // Combine date + time into scheduled_at
        $request->merge(['scheduled_at' => $request->meeting_date . ' ' . $request->meeting_time . ':00']);

        DB::beginTransaction();
        try {
            $mode        = (int) $request->mode;
            $isRecurring = $request->boolean('is_recurring') && $request->recurrence_type && $request->recurrence_end_date;
            $virtualLink = $request->virtual_link;
            $spaceId     = null;

            if (in_array($mode, [MEETING_MODE_VIRTUAL, MEETING_MODE_HYBRID])
                && $request->create_google_meet
                && GoogleMeetService::isConfigured()) {
                try {
                    $space       = (new GoogleMeetService())->createSpace();
                    $virtualLink = $space['meetingUri'];
                    $spaceId     = $space['name'];
                } catch (\Exception $gmEx) {
                    \Log::warning('GoogleMeetService::createSpace failed: ' . $gmEx->getMessage());
                }
            }

            $count   = GovernanceMeeting::where('owner_user_id', auth()->id())->count() + 1;
            $meeting = GovernanceMeeting::create([
                'owner_user_id'            => auth()->id(),
                'created_by'               => auth()->id(),
                'title'                    => $request->title,
                'reference_number'         => 'MTG-' . now()->format('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT),
                'type'                     => $request->type,
                'mode'                     => $mode,
                'status'                   => MEETING_STATUS_SCHEDULED,
                'chairman'                 => $request->chairman,
                'agenda'                   => $request->agenda,
                'meeting_objectives'       => $request->meeting_objectives,
                'venue'                    => $request->venue,
                'virtual_link'             => $virtualLink,
                'google_meet_space_id'     => $spaceId,
                'scheduled_at'             => $request->scheduled_at,
                'notes'                    => $request->notes,
                'is_recurring'             => $isRecurring,
                'recurrence_type'          => $isRecurring ? $request->recurrence_type : null,
                'recurrence_day_of_week'   => $isRecurring ? $request->recurrence_day_of_week : null,
                'recurrence_day_of_month'  => $isRecurring ? $request->recurrence_day_of_month : null,
                'recurrence_end_date'      => $isRecurring ? $request->recurrence_end_date : null,
            ]);

            $shareholders = collect();
            if ($request->invite_all_shareholders) {
                $shareholders = Shareholder::where('owner_user_id', auth()->id())
                    ->where('status', SHAREHOLDER_STATUS_ACTIVE)->get();
                foreach ($shareholders as $sh) {
                    MeetingAttendee::firstOrCreate(
                        ['meeting_id' => $meeting->id, 'shareholder_id' => $sh->id],
                        ['invited' => true]
                    );
                }
            }

            // ── Generate recurring instances ──────────────────────────────────
            $instances = [];
            if ($isRecurring) {
                $instances = $this->generateRecurringDates(
                    \Carbon\Carbon::parse($request->scheduled_at),
                    $request->recurrence_type,
                    (int) ($request->recurrence_day_of_week ?? 0),
                    (int) ($request->recurrence_day_of_month ?? 1),
                    \Carbon\Carbon::parse($request->recurrence_end_date)->endOfDay()
                );

                foreach ($instances as $instanceDate) {
                    $count++;
                    $child = GovernanceMeeting::create([
                        'owner_user_id'        => auth()->id(),
                        'created_by'           => auth()->id(),
                        'title'                => $meeting->title,
                        'reference_number'     => 'MTG-' . now()->format('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT),
                        'type'                 => $meeting->type,
                        'mode'                 => $meeting->mode,
                        'status'               => MEETING_STATUS_SCHEDULED,
                        'agenda'               => $meeting->agenda,
                        'venue'                => $meeting->venue,
                        'virtual_link'         => $meeting->virtual_link,
                        'scheduled_at'         => $instanceDate,
                        'notes'                => $meeting->notes,
                        'is_recurring'         => true,
                        'parent_meeting_id'    => $meeting->id,
                        'recurrence_type'      => $meeting->recurrence_type,
                        'recurrence_end_date'  => $meeting->recurrence_end_date,
                    ]);

                    if ($shareholders->isNotEmpty()) {
                        foreach ($shareholders as $sh) {
                            MeetingAttendee::firstOrCreate(
                                ['meeting_id' => $child->id, 'shareholder_id' => $sh->id],
                                ['invited' => true]
                            );
                        }
                    }
                }
            }

            // ── Notify shareholders ───────────────────────────────────────────
            if ($shareholders->isNotEmpty()) {
                $recurringNote = $isRecurring
                    ? (' This is a recurring meeting with ' . count($instances) . ' scheduled occurrence(s).')
                    : '';
                ShareholderNotification::notifyAll(
                    auth()->id(), 'meeting',
                    'Meeting Scheduled: ' . $meeting->title,
                    'A ' . ($mode == MEETING_MODE_VIRTUAL ? 'virtual ' : '') . 'meeting has been scheduled for '
                        . \Carbon\Carbon::parse($request->scheduled_at)->format('d M Y, H:i') . '.' . $recurringNote,
                    route('shareholder.meetings.show', $meeting->id),
                    $meeting
                );
            }

            DB::commit();

            $msg = __('Meeting created successfully.');
            if ($isRecurring) $msg .= ' ' . count($instances) . ' ' . __('recurring instances generated.');
            if ($spaceId)     $msg .= ' ' . __('Google Meet link generated.');
            return $this->success(['meeting_id' => $meeting->id, 'virtual_link' => $virtualLink], $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * Generate all occurrence dates for a recurring meeting series.
     * Returns dates strictly AFTER $start (the first occurrence is the parent meeting itself).
     */
    private function generateRecurringDates(
        \Carbon\Carbon $start,
        string $type,
        int $dayOfWeek,
        int $dayOfMonth,
        \Carbon\Carbon $endDate
    ): array {
        $dates   = [];
        $timeStr = $start->format('H:i:s');

        if ($type === 'weekly') {
            $current = $start->copy()->addDay();
            // Advance to next matching weekday
            while ($current->dayOfWeek !== $dayOfWeek) {
                $current->addDay();
            }
            while ($current->lte($endDate)) {
                $dates[] = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $current->format('Y-m-d') . ' ' . $timeStr);
                $current->addWeek();
            }
        } elseif ($type === 'monthly') {
            $current = $start->copy()->addDay()->startOfMonth();
            // Advance to the target day-of-month
            while ($current->day !== $dayOfMonth) {
                $current->addDay();
                if ($current->day === 1) break; // overflow — day not in this month, skip to next
            }
            if ($current->lte($start)) $current->addMonthWithoutOverflow();
            while ($current->lte($endDate)) {
                $dates[] = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $current->format('Y-m-d') . ' ' . $timeStr);
                $current->addMonthWithoutOverflow();
                // Find the correct day in the new month
                $targetDay = min($dayOfMonth, $current->daysInMonth);
                $current->day($targetDay);
            }
        }

        return $dates;
    }

    public function meetingShow(GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);
        $meeting->load('attendees.shareholder.user', 'documents.file', 'resolutions', 'createdBy');

        $data['pageTitle']       = $meeting->title;
        $data['meeting']         = $meeting;
        $data['googleMeetReady'] = GoogleMeetService::isConfigured();
        return view('admin.governance.meetings.show', $data);
    }

    public function meetingUpdateMinutes(Request $request, GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);
        $request->validate(['minutes' => 'nullable|string']);
        $meeting->update(['minutes' => $request->minutes]);
        return $this->success([], __('Minutes saved successfully.'));
    }

    public function meetingUpdateRecording(Request $request, GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);
        $request->validate(['recording_url' => 'nullable|url|max:500']);
        $meeting->update(['recording_url' => $request->recording_url]);
        return $this->success([], __('Recording URL saved.'));
    }

    public function meetingUpdateStatus(Request $request, GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);
        $request->validate([
            'status'     => 'required|in:1,2,3',
            'started_at' => 'nullable|date',
            'ended_at'   => 'nullable|date',
        ]);
        $meeting->update([
            'status'     => $request->status,
            'started_at' => $request->started_at,
            'ended_at'   => $request->ended_at,
            'duration_minutes' => ($request->started_at && $request->ended_at)
                ? \Carbon\Carbon::parse($request->started_at)->diffInMinutes(\Carbon\Carbon::parse($request->ended_at))
                : $meeting->duration_minutes,
        ]);
        return $this->success([], __('Meeting status updated.'));
    }

    public function meetingGenerateGoogleMeet(GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);

        if (!GoogleMeetService::isConfigured()) {
            return $this->error([], __('Google Meet is not configured. Please set GOOGLE_MEET_SERVICE_ACCOUNT_JSON in your .env file.'));
        }

        try {
            $space = (new GoogleMeetService())->createSpace();
            $meeting->update([
                'virtual_link'        => $space['meetingUri'],
                'google_meet_space_id'=> $space['name'],
                'mode'                => MEETING_MODE_VIRTUAL,
            ]);
            return $this->success([
                'virtual_link'        => $space['meetingUri'],
                'google_meet_space_id'=> $space['name'],
            ], __('Google Meet link generated successfully.'));
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function meetingUploadDocument(Request $request, GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);
        $request->validate([
            'title'         => 'required|max:191',
            'document_type' => 'required',
            'file'          => 'required|file|max:20480',
        ]);

        DB::beginTransaction();
        try {
            $fileManager = new FileManager();
            $upload = $fileManager->upload('MeetingDocument', $request->file('file'));
            if (!$upload['status']) throw new \Exception($upload['message']);

            GovernanceDocument::create([
                'owner_user_id'   => auth()->id(),
                'uploaded_by'     => auth()->id(),
                'title'           => $request->title,
                'document_type'   => $request->document_type,
                'description'     => $request->description,
                'file_id'         => $upload['file']->id,
                'version'         => '1.0',
                'status'          => DOC_STATUS_ACTIVE,
                'all_shareholders'=> (bool) $request->get('all_shareholders', false),
                'meeting_id'      => $meeting->id,
            ]);

            DB::commit();
            return $this->success([], __('Document uploaded successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function meetingUpdate(Request $request, GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);

        $request->validate([
            'title'        => 'required|max:191',
            'type'         => 'required|in:1,2,3,4',
            'mode'         => 'required|in:1,2,3',
            'meeting_date' => 'required|date_format:Y-m-d',
            'meeting_time' => 'required|date_format:H:i',
            'chairman'     => 'nullable|string|max:191',
            'venue'        => 'nullable|string|max:500',
            'virtual_link' => 'nullable|url|max:500',
            'agenda'       => 'nullable|string',
            'meeting_objectives' => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $scheduledAt = $request->meeting_date . ' ' . $request->meeting_time . ':00';

        $meeting->update([
            'title'               => $request->title,
            'type'                => $request->type,
            'mode'                => (int) $request->mode,
            'scheduled_at'        => $scheduledAt,
            'chairman'            => $request->chairman,
            'venue'               => $request->venue,
            'virtual_link'        => $request->virtual_link,
            'agenda'              => $request->agenda,
            'meeting_objectives'  => $request->meeting_objectives,
            'notes'               => $request->notes,
        ]);

        return $this->success([], __('Meeting updated successfully.'));
    }

    public function meetingReschedule(Request $request, GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);

        $request->validate([
            'meeting_date'  => 'required|date_format:Y-m-d',
            'meeting_time'  => 'required|date_format:H:i',
            'notify_shareholders' => 'nullable|boolean',
        ]);

        $oldDate     = $meeting->scheduled_at ? $meeting->scheduled_at->format('d M Y, H:i') : '—';
        $scheduledAt = $request->meeting_date . ' ' . $request->meeting_time . ':00';
        $meeting->update(['scheduled_at' => $scheduledAt]);

        $newDate = \Carbon\Carbon::parse($scheduledAt)->format('d M Y, H:i');

        if ($request->boolean('notify_shareholders', true)) {
            ShareholderNotification::notifyAll(
                auth()->id(), 'meeting',
                'Meeting Rescheduled: ' . $meeting->title,
                "The meeting \"{$meeting->title}\" has been rescheduled from {$oldDate} to {$newDate}.",
                route('shareholder.meetings.show', $meeting->id),
                $meeting
            );
        }

        return $this->success(['scheduled_at' => $newDate], __('Meeting rescheduled successfully.'));
    }

    public function meetingDestroy(GovernanceMeeting $meeting)
    {
        abort_if($meeting->owner_user_id !== auth()->id(), 403);
        $meeting->delete();
        return $this->success([], __('Meeting deleted successfully.'));
    }

    public function meetingBulkAction(Request $request)
    {
        $request->validate([
            'action'  => 'required|in:delete,cancel,complete',
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'integer',
        ]);

        $meetings = GovernanceMeeting::where('owner_user_id', auth()->id())
            ->whereIn('id', $request->ids)
            ->get();

        $count = 0;
        foreach ($meetings as $meeting) {
            if ($request->action === 'delete') {
                $meeting->delete();
            } elseif ($request->action === 'cancel') {
                $meeting->update(['status' => MEETING_STATUS_CANCELLED]);
            } elseif ($request->action === 'complete') {
                $meeting->update(['status' => MEETING_STATUS_COMPLETED]);
            }
            $count++;
        }

        $labels = ['delete' => 'deleted', 'cancel' => 'cancelled', 'complete' => 'marked as completed'];
        return $this->success(['count' => $count], "{$count} meeting(s) {$labels[$request->action]} successfully.");
    }

    // ---- Audit Logs ----
    public function auditLogs(Request $request)
    {
        $data['pageTitle'] = __('Governance Audit Log');
        $query = GovernanceAuditLog::where('owner_user_id', auth()->id())->with('user')->orderByDesc('occurred_at');
        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        $data['logs']    = $query->paginate(50);
        $data['actions'] = GovernanceAuditLog::where('owner_user_id', auth()->id())->distinct()->pluck('action');
        return view('admin.governance.audit-logs', $data);
    }
}
