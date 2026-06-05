<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\RentCollection;
use App\Models\AccountantExpense;
use App\Models\TenantBalanceLedger;
use App\Models\Tenant;
use App\Models\Property;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $accountant  = auth()->user()->accountant;
        $ownerUserId = $accountant->owner_user_id;

        $today     = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $monthStart= now()->startOfMonth()->toDateString();

        // KPI: collections sums (confirmed only)
        $todayCollections = RentCollection::where('owner_user_id', $ownerUserId)
            ->where('status', COLLECTION_STATUS_CONFIRMED)
            ->whereDate('payment_date', $today)
            ->sum('amount');

        $weekCollections = RentCollection::where('owner_user_id', $ownerUserId)
            ->where('status', COLLECTION_STATUS_CONFIRMED)
            ->whereBetween('payment_date', [$weekStart, $today])
            ->sum('amount');

        $monthCollections = RentCollection::where('owner_user_id', $ownerUserId)
            ->where('status', COLLECTION_STATUS_CONFIRMED)
            ->whereBetween('payment_date', [$monthStart, $today])
            ->sum('amount');

        // Pending expenses count
        $pendingExpenses = AccountantExpense::where('owner_user_id', $ownerUserId)
            ->where('status', EXPENSE_STATUS_PENDING)
            ->count();

        // Total tenants for this owner
        $totalTenants = Tenant::where('owner_user_id', $ownerUserId)->count();

        // Overdue balances (tenants with outstanding balance_due)
        $overdueBalances = TenantBalanceLedger::where('owner_user_id', $ownerUserId)
            ->where('balance_due', '>', 0)
            ->count();

        // Recent collections (last 5) with tenant loaded
        $recentCollections = RentCollection::where('owner_user_id', $ownerUserId)
            ->with('tenant')
            ->orderByDesc('payment_date')
            ->limit(5)
            ->get();

        // Recent expenses (last 5)
        $recentExpenses = AccountantExpense::where('owner_user_id', $ownerUserId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Top 5 properties by total confirmed collections this month
        $topProperties = RentCollection::where('owner_user_id', $ownerUserId)
            ->where('status', COLLECTION_STATUS_CONFIRMED)
            ->whereBetween('payment_date', [$monthStart, $today])
            ->select('property_id', DB::raw('SUM(amount) as total_collected'))
            ->groupBy('property_id')
            ->orderByDesc('total_collected')
            ->limit(5)
            ->with('property')
            ->get();

        // 12-month collections chart
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $monthlyCollections = [];
        $monthlyExpenses = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyCollections[] = (float)\App\Models\RentCollection::where('owner_user_id', $ownerUserId)
                ->where('status', 1) // confirmed
                ->whereMonth('payment_date', $m)
                ->whereYear('payment_date', now()->year)
                ->sum('amount');
            $monthlyExpenses[] = (float)\App\Models\AccountantExpense::where('owner_user_id', $ownerUserId)
                ->where('status', '!=', 3) // not rejected
                ->whereMonth('created_at', $m)
                ->whereYear('created_at', now()->year)
                ->sum('amount');
        }

        // Collections by payment method for pie chart
        $byMethod = \App\Models\RentCollection::where('owner_user_id', $ownerUserId)
            ->where('status', 1)
            ->whereYear('payment_date', now()->year)
            ->select('payment_method', DB::raw('sum(amount) as total'))
            ->groupBy('payment_method')
            ->get();
        $methodLabels = [1=>'Cash',2=>'Bank Transfer',3=>'MTN MoMo',4=>'Airtel Money',5=>'Cheque',6=>'Card'];

        // Top properties by collections (annual)
        $topPropertiesAnnual = \App\Models\RentCollection::where('owner_user_id', $ownerUserId)
            ->where('status', 1)->whereYear('payment_date', now()->year)
            ->with('property')
            ->select('property_id', DB::raw('sum(amount) as total'))
            ->groupBy('property_id')
            ->orderByDesc('total')
            ->limit(5)->get();

        $data = [
            'pageTitle'           => 'Dashboard',
            'todayCollections'    => $todayCollections,
            'weekCollections'     => $weekCollections,
            'monthCollections'    => $monthCollections,
            'pendingExpenses'     => $pendingExpenses,
            'totalTenants'        => $totalTenants,
            'overdueBalances'     => $overdueBalances,
            'recentCollections'   => $recentCollections,
            'recentExpenses'      => $recentExpenses,
            'topProperties'       => $topProperties,
            'chartMonths'         => $months,
            'monthlyCollections'  => $monthlyCollections,
            'monthlyExpenses'     => $monthlyExpenses,
            'pieLabels'           => $byMethod->map(fn($r) => $methodLabels[$r->payment_method] ?? 'Other')->values()->toArray(),
            'pieValues'           => $byMethod->pluck('total')->map(fn($v) => (float)$v)->values()->toArray(),
            'topPropertiesAnnual' => $topPropertiesAnnual,
        ];

        return view('accountant.dashboard', $data);
    }
}
