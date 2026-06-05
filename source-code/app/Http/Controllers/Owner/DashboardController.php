<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AccountantExpense;
use App\Models\Expense;
use App\Models\FinancialApproval;
use App\Models\FinancialAuditLog;
use App\Models\GovernanceDocument;
use App\Models\GovernanceMeeting;
use App\Models\Invoice;
use App\Models\Maintainer;
use App\Models\Notification;
use App\Models\Property;
use App\Models\PropertyUnit;
use App\Models\RentCollection;
use App\Models\Resolution;
use App\Models\Tenant;
use App\Services\OwnerService;
use App\Services\PropertyService;
use App\Services\SmartAlertsService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public $propertyService;
    public $ticketService;

    public function __construct()
    {
        $this->propertyService = new PropertyService;
        $this->ticketService = new TicketService;
    }

    public function dashboard()
    {
        $activeRole = session('active_role');

        // Dispatch to role-specific dashboards
        switch ($activeRole) {
            case 'director':           return $this->directorDashboard();
            case 'finance_manager':    return $this->financeManagerDashboard();
            case 'landlord':           return $this->landlordDashboard();
            case 'auditor':            return $this->auditorDashboard();
            case 'compliance_officer': return $this->complianceDashboard();
            case 'secretary':          return $this->secretaryDashboard();
            case 'tenant_manager':     return $this->tenantManagerDashboard();
            default:                   return $this->ownerDashboard();
        }
    }

    private function ownerDashboard()
    {
        $data['pageTitle'] = __('Dashboard');
        $data['totalProperties'] = Property::where('owner_user_id', getOwnerUserId())->count();
        $data['totalUnits'] = PropertyUnit::query()->join('properties', 'property_units.property_id', '=', 'properties.id')->where('properties.owner_user_id', getOwnerUserId())->count();
        $data['totalTenants'] = Tenant::where('owner_user_id', getOwnerUserId())->where('status', TENANT_STATUS_ACTIVE)->count();
        $data['properties'] = $this->propertyService->getAllCount()->take(3);
        $data['tickets'] = $this->ticketService->getAll();
        $data['totalMaintainers'] = Maintainer::where('owner_user_id', getOwnerUserId())->count();

        // Chart Rent overview
        $data['months'] = array_values(month());
        $invoices = Invoice::query()
            ->select(DB::raw('sum(amount) as `total`'), DB::raw("month"), DB::raw('max(created_at) as createdAt'))
            ->whereYear('created_at', date('Y'))->groupBy('month')
            ->where('owner_user_id', getOwnerUserId())->where('status', INVOICE_STATUS_PAID)->get();
        $data['yearlyTotalAmount'] = $invoices->sum('total');

        $invoiceMonthlyAmount = [];
        foreach ($data['months'] as $month) {
            $valueMonth = $invoices->where('month', $month)->first();
            array_push($invoiceMonthlyAmount, $valueMonth ? $valueMonth->total : 0);
        }
        $data['invoiceMonthlyAmount'] = $invoiceMonthlyAmount;

        // Smart alerts
        $data['alerts'] = (new SmartAlertsService())->getAlerts(getOwnerUserId());

        return view('owner.dashboard')->with($data);
    }

    public function tenantManagerDashboard()
    {
        $ownerUserId = getOwnerUserId();
        $data['pageTitle'] = __('Tenant Manager Dashboard');

        $data['totalTenants']    = \App\Models\Tenant::where('owner_user_id', $ownerUserId)->count();
        $data['activeTenants']   = \App\Models\Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->count();
        $data['inactiveTenants'] = \App\Models\Tenant::where('owner_user_id', $ownerUserId)->where('status', '!=', TENANT_STATUS_ACTIVE)->count();
        $data['totalUnits']      = \App\Models\PropertyUnit::join('properties', 'property_units.property_id', '=', 'properties.id')->where('properties.owner_user_id', $ownerUserId)->count();
        $data['occupiedUnits']   = \App\Models\Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->whereNotNull('unit_id')->distinct('unit_id')->count('unit_id');
        $data['vacantUnits']     = $data['totalUnits'] - $data['occupiedUnits'];
        $data['openTickets']     = \App\Models\Ticket::where('owner_user_id', $ownerUserId)->where('status', '!=', TICKET_STATUS_CLOSE)->count();
        $data['pendingInvoices'] = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count();

        $data['recentTenants']  = \App\Models\Tenant::where('owner_user_id', $ownerUserId)
            ->with('user', 'unit', 'property')
            ->orderByDesc('created_at')->limit(8)->get();

        $data['expiringLeases'] = \App\Models\Tenant::where('owner_user_id', $ownerUserId)
            ->where('status', TENANT_STATUS_ACTIVE)
            ->whereNotNull('lease_end_date')
            ->where('lease_end_date', '<=', now()->addDays(30))
            ->where('lease_end_date', '>=', now())
            ->orderBy('lease_end_date')->limit(5)->get();

        $data['recentTickets']  = \App\Models\Ticket::where('owner_user_id', $ownerUserId)
            ->with('user')->orderByDesc('created_at')->limit(5)->get();

        return view('owner.role-dashboards.tenant-manager', $data);
    }

    public function notification()
    {
        $data['pageTitle'] = __('Notification');
        Notification::query()
            ->where(function ($q) {
                $q->where('notifications.user_id', getOwnerUserId())
                    ->orWhere('notifications.user_id', null);
            })
            ->update(['is_seen' => ACTIVE]);
        return view('owner.notification')->with($data);
    }

    public function auditorDashboard()
    {
        $ownerUserId = getOwnerUserId();
        $data['pageTitle'] = __('Auditor Dashboard');

        $data['totalAuditLogs']     = FinancialAuditLog::where('owner_user_id', $ownerUserId)->count();
        $data['recentLogs']         = FinancialAuditLog::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->limit(15)->get();
        $data['todayLogs']          = FinancialAuditLog::where('owner_user_id', $ownerUserId)->whereDate('created_at', today())->count();
        $data['weekLogs']           = FinancialAuditLog::where('owner_user_id', $ownerUserId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Financial summary for audit
        $data['totalRevenue']       = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount');
        $data['totalExpenses']      = Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount');
        $data['unpaidInvoices']     = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count();
        $data['pendingApprovals']   = FinancialApproval::where('owner_user_id', $ownerUserId)->where('status', APPROVAL_STATUS_PENDING)->count();

        // Suspicious activity: large transactions (top 5 by amount)
        $data['largeTransactions']  = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->orderByDesc('amount')->limit(5)->with('tenant')->get();

        // Log actions summary
        $data['actionSummary']      = FinancialAuditLog::where('owner_user_id', $ownerUserId)
            ->select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('owner.role-dashboards.auditor', $data);
    }

    public function complianceDashboard()
    {
        $ownerUserId = getOwnerUserId();
        $data['pageTitle'] = __('Compliance Officer Dashboard');

        $data['totalDocuments']     = GovernanceDocument::where('owner_user_id', $ownerUserId)->count();
        $data['recentDocuments']    = GovernanceDocument::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->limit(8)->get();
        $data['totalResolutions']   = Resolution::where('owner_user_id', $ownerUserId)->count();
        $data['passedResolutions']  = Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_PASSED)->count();
        $data['failedResolutions']  = Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_FAILED)->count();
        $data['openResolutions']    = Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_OPEN)->count();
        $data['totalTenants']       = Tenant::where('owner_user_id', $ownerUserId)->count();
        $data['activeTenants']      = Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->count();
        $data['recentAuditLogs']    = FinancialAuditLog::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->limit(10)->get();

        // Compliance alerts (rule-based)
        $data['complianceAlerts'] = [];
        if ($data['openResolutions'] > 0) {
            $data['complianceAlerts'][] = ['type' => 'warning', 'message' => $data['openResolutions'] . ' resolutions awaiting vote/decision'];
        }
        $data['unpaidInvoicesCount'] = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count();
        if ($data['unpaidInvoicesCount'] > 5) {
            $data['complianceAlerts'][] = ['type' => 'danger', 'message' => $data['unpaidInvoicesCount'] . ' unpaid invoices — compliance risk'];
        }
        if ($data['totalDocuments'] == 0) {
            $data['complianceAlerts'][] = ['type' => 'info', 'message' => 'No governance documents uploaded yet'];
        }

        return view('owner.role-dashboards.compliance', $data);
    }

    public function secretaryDashboard()
    {
        $ownerUserId = getOwnerUserId();
        $data['pageTitle'] = __('Secretary Dashboard');

        $data['upcomingMeetings']   = GovernanceMeeting::where('owner_user_id', $ownerUserId)
            ->where('status', MEETING_STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit(5)->get();
        $data['pastMeetings']       = GovernanceMeeting::where('owner_user_id', $ownerUserId)
            ->whereIn('status', [MEETING_STATUS_COMPLETED, MEETING_STATUS_CANCELLED])
            ->orderByDesc('scheduled_at')->limit(5)->get();
        $data['totalMeetings']      = GovernanceMeeting::where('owner_user_id', $ownerUserId)->count();
        $data['openResolutions']    = Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_OPEN)->orderBy('voting_closes_at')->limit(5)->get();
        $data['totalDocuments']     = GovernanceDocument::where('owner_user_id', $ownerUserId)->count();
        $data['recentDocuments']    = GovernanceDocument::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->limit(5)->get();
        $data['totalResolutions']   = Resolution::where('owner_user_id', $ownerUserId)->count();

        // Calendar events: all meetings in next 30 days
        $data['calendarMeetings']   = GovernanceMeeting::where('owner_user_id', $ownerUserId)
            ->whereBetween('scheduled_at', [now(), now()->addDays(30)])
            ->orderBy('scheduled_at')->get();

        return view('owner.role-dashboards.secretary', $data);
    }

    public function directorDashboard()
    {
        $ownerUserId = getOwnerUserId();
        $data['pageTitle'] = __('Director Dashboard');

        // Executive KPIs
        $data['totalProperties']    = Property::where('owner_user_id', $ownerUserId)->count();
        $data['totalUnits']         = PropertyUnit::join('properties','property_units.property_id','=','properties.id')->where('properties.owner_user_id',$ownerUserId)->count();
        $data['occupiedUnits']      = Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->whereNotNull('unit_id')->distinct('unit_id')->count('unit_id');
        $data['occupancyRate']      = $data['totalUnits'] > 0 ? round(($data['occupiedUnits'] / $data['totalUnits']) * 100, 1) : 0;
        $data['monthlyRevenue']     = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereMonth('created_at', now()->month)->sum('amount');
        $data['yearlyRevenue']      = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount');
        $data['totalExpenses']      = Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount');
        $data['netProfit']          = $data['yearlyRevenue'] - $data['totalExpenses'];
        $data['pendingApprovals']   = FinancialApproval::where('owner_user_id', $ownerUserId)->where('status', APPROVAL_STATUS_PENDING)->count();
        $data['openResolutions']    = Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_OPEN)->count();
        $data['upcomingMeetings']   = GovernanceMeeting::where('owner_user_id', $ownerUserId)->where('status', MEETING_STATUS_SCHEDULED)->where('scheduled_at', '>', now())->orderBy('scheduled_at')->limit(5)->get();
        $data['recentApprovals']    = FinancialApproval::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->limit(5)->get();

        // 12-month revenue chart
        $data['months'] = array_values(month());
        $invoices = Invoice::select(DB::raw('sum(amount) as total'), DB::raw('month'), DB::raw('max(created_at) as createdAt'))
            ->whereYear('created_at', date('Y'))->groupBy('month')
            ->where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->get();
        $monthlyRevenue = [];
        foreach ($data['months'] as $m) {
            $val = $invoices->where('month', $m)->first();
            $monthlyRevenue[] = $val ? (float)$val->total : 0;
        }
        $data['monthlyRevenueChart'] = $monthlyRevenue;

        return view('owner.role-dashboards.director', $data);
    }

    public function financeManagerDashboard()
    {
        $ownerUserId = getOwnerUserId();
        $data['pageTitle'] = __('Finance Manager Dashboard');

        // Financial KPIs
        $data['totalRevenue']         = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount');
        $data['totalExpenses']        = Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount');
        $data['netProfit']            = $data['totalRevenue'] - $data['totalExpenses'];
        $data['outstandingBalance']   = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->sum('amount');
        $data['monthRevenue']         = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');
        $data['monthExpenses']        = Expense::where('owner_user_id', $ownerUserId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount');
        $data['rentCollections']      = RentCollection::where('owner_user_id', $ownerUserId)->thisMonth()->confirmed()->sum('amount');
        $data['pendingExpenses']      = AccountantExpense::where('owner_user_id', $ownerUserId)->where('status', 0)->count();
        $data['recentInvoices']       = Invoice::where('owner_user_id', $ownerUserId)->with('tenant')->orderByDesc('created_at')->limit(8)->get();
        $data['recentExpensesList']   = Expense::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->limit(5)->get();

        // Monthly Revenue vs Expenses chart
        $data['months'] = array_values(month());
        $invoices = Invoice::select(DB::raw('sum(amount) as total'), DB::raw('month'))
            ->whereYear('created_at', date('Y'))->groupBy('month')
            ->where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->get();
        $expenses = Expense::select(DB::raw('sum(total_amount) as total'), DB::raw('MONTH(created_at) as month_num'))
            ->whereYear('created_at', date('Y'))->groupBy('month_num')
            ->where('owner_user_id', $ownerUserId)->get();

        $revChart = []; $expChart = [];
        foreach (range(1, 12) as $i) {
            $revChart[] = (float)($invoices->where('month', $i)->first()?->total ?? 0);
            $expChart[] = (float)($expenses->where('month_num', $i)->first()?->total ?? 0);
        }
        $data['revenueChart']  = $revChart;
        $data['expenseChart']  = $expChart;

        return view('owner.role-dashboards.finance-manager', $data);
    }

    public function landlordDashboard()
    {
        $ownerUserId = getOwnerUserId();
        $data['pageTitle'] = __('Landlord Dashboard');

        $data['totalProperties']  = Property::where('owner_user_id', $ownerUserId)->count();
        $data['totalUnits']       = PropertyUnit::join('properties','property_units.property_id','=','properties.id')->where('properties.owner_user_id',$ownerUserId)->count();
        $data['totalTenants']     = Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->count();
        $data['monthlyIncome']    = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');
        $data['yearlyIncome']     = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount');
        $data['pendingPayments']  = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count();
        $data['maintenanceCost']  = Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount');
        $data['properties']       = Property::where('owner_user_id', $ownerUserId)->with('propertyUnits.activeTenant')->limit(6)->get();
        $data['recentPayments']   = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->with('tenant')->orderByDesc('created_at')->limit(8)->get();

        // Monthly income chart
        $data['months'] = array_values(month());
        $invoices = Invoice::select(DB::raw('sum(amount) as total'), DB::raw('month'))
            ->whereYear('created_at', date('Y'))->groupBy('month')
            ->where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->get();
        $incomeChart = [];
        foreach ($data['months'] as $m) {
            $incomeChart[] = (float)($invoices->where('month', $m)->first()?->total ?? 0);
        }
        $data['incomeChart'] = $incomeChart;

        return view('owner.role-dashboards.landlord', $data);
    }

    public function topSearch(Request $request)
    {
        $data['status'] = false;
        if ($request->keyword) {
            $ownerService = new OwnerService;
            $searchContent = $ownerService->topSearch($request);
            $data['data'] = view('owner.top-search-append', $searchContent)->render();
            $data['status'] = $searchContent['status'];
        }
        return response()->json($data);
    }
}
