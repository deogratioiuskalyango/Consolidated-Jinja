<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\DividendDeclaration;
use App\Models\FinancialApproval;
use App\Models\GovernanceMeeting;
use App\Models\Resolution;
use App\Models\ShareholderNotification;
use App\Services\GovernanceEngine;

class DashboardController extends Controller
{
    public function __construct(private GovernanceEngine $engine) {}

    public function index()
    {
        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions', 'user');
        $ownerUserId = $shareholder->owner_user_id;

        $data['pageTitle']   = __('Shareholder Dashboard');
        $data['shareholder'] = $shareholder;

        // Governance rights summary (drives dynamic UI)
        $data['rights'] = $this->engine->getRightsSummary($shareholder);

        // ── Core share stats (used directly by view) ────────────────────────
        $data['totalShares']      = $shareholder->total_shares;
        $data['ownershipPercent'] = $shareholder->ownership_percentage;

        // ── Open resolutions this shareholder can vote on ───────────────────
        $data['openResolutions'] = Resolution::where('owner_user_id', $ownerUserId)
            ->where('status', RESOLUTION_STATUS_OPEN)
            ->orderBy('voting_closes_at')
            ->limit(5)
            ->get();

        // ── Pending approvals collection ────────────────────────────────────
        $data['pendingApprovals'] = FinancialApproval::where('owner_user_id', $ownerUserId)
            ->where('status', APPROVAL_STATUS_PENDING)
            ->whereDoesntHave('actions', fn($q) => $q->where('shareholder_id', $shareholder->id))
            ->limit(5)
            ->get();

        // ── Upcoming meetings ───────────────────────────────────────────────
        $data['upcomingMeetings'] = GovernanceMeeting::where('owner_user_id', $ownerUserId)
            ->where('status', MEETING_STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit(3)
            ->get();

        // ── Recent dividends ────────────────────────────────────────────────
        $data['recentDividends'] = \App\Models\DividendDeclaration::where('owner_user_id', $ownerUserId)
            ->latest('declaration_date')
            ->limit(5)
            ->get();

        // ── Unread notification count ───────────────────────────────────────
        $data['unreadNotifications'] = ShareholderNotification::where(function ($q) use ($shareholder) {
                $q->where('shareholder_id', $shareholder->id)
                  ->orWhere(fn($q2) => $q2->where('owner_user_id', $shareholder->owner_user_id)->where('is_broadcast', true));
            })
            ->where('is_read', false)->count();

        // Financial chart data for shareholder view
        $data['chartMonths'] = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // Monthly dividend data
        $dividends = \App\Models\DividendDeclaration::where('owner_user_id', $ownerUserId)
            ->whereYear('declaration_date', now()->year)
            ->select(\Illuminate\Support\Facades\DB::raw('MONTH(declaration_date) as m'), \Illuminate\Support\Facades\DB::raw('sum(total_amount) as total'))
            ->groupBy('m')->get();
        $monthlyDividends = array_fill(0, 12, 0);
        foreach ($dividends as $d) { $monthlyDividends[$d->m - 1] = (float)$d->total; }
        $data['monthlyDividends'] = $monthlyDividends;

        // Company monthly revenue (from confirmed rent collections for this owner)
        $revenue = \App\Models\RentCollection::where('owner_user_id', $ownerUserId)->where('status', 1)
            ->whereYear('payment_date', now()->year)
            ->select(\Illuminate\Support\Facades\DB::raw('MONTH(payment_date) as m'), \Illuminate\Support\Facades\DB::raw('sum(amount) as total'))
            ->groupBy('m')->get();
        $monthlyRevenue = array_fill(0, 12, 0);
        foreach ($revenue as $r) { $monthlyRevenue[$r->m - 1] = (float)$r->total; }
        $data['monthlyRevenue'] = $monthlyRevenue;

        $data['totalDividendsThisYear'] = array_sum($data['monthlyDividends']);
        $data['totalRevenueThisYear']   = array_sum($data['monthlyRevenue']);

        return view('shareholder.dashboard', $data);
    }

    public function governanceRights()
    {
        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');
        $data['pageTitle']   = __('My Governance Rights');
        $data['shareholder'] = $shareholder;
        $data['rights']      = $this->engine->getRightsSummary($shareholder);
        $data['shareClass']  = $shareholder->shareClass->load('permissions');
        $data['thresholds']  = $this->engine->getThresholds($shareholder->owner_user_id);
        return view('shareholder.governance-rights', $data);
    }
}
