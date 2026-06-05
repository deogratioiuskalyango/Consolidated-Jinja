<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Resolution;
use App\Models\ResolutionVote;
use App\Models\GovernanceAuditLog;
use App\Services\GovernanceEngine;
use Illuminate\Http\Request;

class VotingController extends Controller
{
    public function __construct(private GovernanceEngine $engine) {}

    public function index()
    {
        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');
        $ownerUserId = $shareholder->owner_user_id;

        $data['pageTitle']   = __('Resolutions & Voting');
        $data['shareholder'] = $shareholder;
        $data['rights']      = $this->engine->getRightsSummary($shareholder);

        $data['resolutions'] = Resolution::where('owner_user_id', $ownerUserId)
            ->with(['votes' => fn($q) => $q->where('shareholder_id', $shareholder->id)])
            ->orderByRaw('status = ? DESC', [RESOLUTION_STATUS_OPEN])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('shareholder.resolutions.index', $data);
    }

    public function show(Resolution $resolution)
    {
        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');
        $this->authorizeResolution($resolution, $shareholder);

        $data['pageTitle']   = $resolution->title;
        $data['resolution']  = $resolution->load('votes.shareholder.user', 'meeting', 'documents');
        $data['myVote']      = $resolution->votes->where('shareholder_id', $shareholder->id)->first();
        $data['shareholder'] = $shareholder;
        $data['canVote']     = $this->engine->canVote($shareholder, $resolution);
        $data['votingWeight']= $this->engine->getVotingWeight($shareholder);
        $data['weightedResults'] = $this->engine->computeWeightedResults($resolution);
        return view('shareholder.resolutions.show', $data);
    }

    public function vote(Request $request, Resolution $resolution)
    {
        $request->validate(['vote' => 'required|in:1,2,3', 'comment' => 'nullable|max:1000']);

        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');
        $this->authorizeResolution($resolution, $shareholder);

        // GovernanceEngine permission check
        if (!$this->engine->canVote($shareholder, $resolution)) {
            return redirect()->back()->with('error', __('Your share class does not permit voting on this resolution type.'));
        }

        if (!$resolution->is_open) {
            return redirect()->back()->with('error', __('Voting is not currently open for this resolution.'));
        }

        if ($resolution->votes()->where('shareholder_id', $shareholder->id)->exists()) {
            return redirect()->back()->with('error', __('You have already voted on this resolution.'));
        }

        // Use GovernanceEngine for weighted vote calculation
        $weight = $this->engine->getVotingWeight($shareholder);

        ResolutionVote::create([
            'resolution_id'  => $resolution->id,
            'shareholder_id' => $shareholder->id,
            'vote'           => $request->vote,
            'voting_weight'  => $weight,
            'comment'        => $request->comment,
            'ip_address'     => $request->ip(),
            'device_info'    => substr($request->userAgent(), 0, 191),
            'voted_at'       => now(),
        ]);

        $resolution->computeResults();

        GovernanceAuditLog::record(
            AUDIT_VOTE,
            $resolution,
            ['vote' => $request->vote, 'weight' => $weight, 'class' => $shareholder->class_code],
            'Cast vote on: ' . $resolution->title
        );

        return redirect()->back()->with('success', __('Your vote has been recorded. Voting weight: ') . number_format($weight, 2));
    }

    private function authorizeResolution(Resolution $resolution, $shareholder): void
    {
        abort_if($resolution->owner_user_id !== $shareholder->owner_user_id, 403);
    }
}
