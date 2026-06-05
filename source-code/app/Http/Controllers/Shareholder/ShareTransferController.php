<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\ShareTransfer;
use App\Models\Shareholder;
use App\Models\GovernanceAuditLog;
use App\Services\GovernanceEngine;
use Illuminate\Http\Request;

class ShareTransferController extends Controller
{
    public function __construct(private GovernanceEngine $engine) {}

    public function index()
    {
        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');

        $data['pageTitle']    = __('Share Transfers');
        $data['shareholder']  = $shareholder;
        $data['canTransfer']  = $this->engine->canTransferShares($shareholder);
        $data['needsBoard']   = $this->engine->transferRequiresBoardApproval($shareholder);
        $data['needsCompliance'] = $this->engine->transferRequiresComplianceReview($shareholder);

        $data['transfers'] = ShareTransfer::where('from_shareholder_id', $shareholder->id)
            ->orWhere('to_shareholder_id', $shareholder->id)
            ->with('fromShareholder.user', 'fromShareholder.shareClass', 'toShareholder.user')
            ->orderByDesc('created_at')
            ->paginate(20);

        // For the transfer modal — other shareholders in same company
        $data['shareholders'] = Shareholder::where('owner_user_id', $shareholder->owner_user_id)
            ->where('id', '!=', $shareholder->id)
            ->where('status', SHAREHOLDER_STATUS_ACTIVE)
            ->with('user', 'shareClass')
            ->get();

        return view('shareholder.share-transfers.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_shareholder_id' => 'required|exists:shareholders,id',
            'shares'            => 'required|numeric|min:0.0001',
            'price_per_share'   => 'nullable|numeric|min:0',
            'reason'            => 'nullable|max:1000',
            'legal_doc'         => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');

        // GovernanceEngine: can this class transfer at all?
        if (!$this->engine->canTransferShares($shareholder)) {
            return redirect()->back()->with('error',
                __('Your share class (') . $shareholder->class_code . __(') does not permit share transfers.')
            );
        }

        if ($request->shares > $shareholder->total_shares) {
            return redirect()->back()->with('error', __('You cannot transfer more shares than you own.'));
        }

        $toShareholder = Shareholder::findOrFail($request->to_shareholder_id);

        $legalFileId = null;
        if ($request->hasFile('legal_doc')) {
            $file   = new \App\Models\FileManager();
            $upload = $file->upload('ShareTransfer', $request->legal_doc);
            if ($upload['status']) $legalFileId = $upload['file']->id;
        }

        // Determine initial status based on transfer rules
        $requiresBoardApproval = $this->engine->transferRequiresBoardApproval($shareholder);
        $status = $requiresBoardApproval ? TRANSFER_STATUS_PENDING : TRANSFER_STATUS_PENDING; // always pending until admin approves

        ShareTransfer::create([
            'owner_user_id'          => $shareholder->owner_user_id,
            'from_shareholder_id'    => $shareholder->id,
            'to_shareholder_id'      => $toShareholder->id,
            'to_name'                => $toShareholder->user?->name,
            'to_email'               => $toShareholder->user?->email,
            'shares'                 => $request->shares,
            'price_per_share'        => $request->price_per_share,
            'total_value'            => $request->price_per_share
                ? round($request->shares * $request->price_per_share, 4)
                : null,
            'reason'                 => $request->reason,
            'legal_document_file_id' => $legalFileId,
            'requested_at'           => now(),
            'status'                 => $status,
        ]);

        $msg = $requiresBoardApproval
            ? __('Transfer request submitted. Board approval required for your share class.')
            : __('Share transfer request submitted for review.');

        GovernanceAuditLog::record(
            AUDIT_SHARE_TRANSFER,
            $shareholder,
            [
                'shares'           => $request->shares,
                'to_shareholder'   => $toShareholder->id,
                'class'            => $shareholder->class_code,
                'needs_board'      => $requiresBoardApproval,
            ],
            'Share transfer request: ' . $request->shares . ' shares to shareholder #' . $toShareholder->id
        );

        return redirect()->back()->with('success', $msg);
    }
}
