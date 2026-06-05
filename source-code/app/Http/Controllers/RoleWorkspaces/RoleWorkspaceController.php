<?php

namespace App\Http\Controllers\RoleWorkspaces;

use App\Http\Controllers\Controller;
use App\Models\AccountantExpense;
use App\Models\Expense;
use App\Models\FinancialApproval;
use App\Models\FinancialAuditLog;
use App\Models\GovernanceDocument;
use App\Models\GovernanceAuditLog;
use App\Models\GovernanceMeeting;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\NoticeBoard;
use App\Models\Property;
use App\Models\PropertyUnit;
use App\Models\Resolution;
use App\Models\SystemUserRole;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Str;

class RoleWorkspaceController extends Controller
{
    /** Per-role dashboard view map. */
    private const VIEW_MAP = [
        'director'           => 'role-workspaces.director',
        'finance_manager'    => 'role-workspaces.finance-manager',
        'landlord'           => 'role-workspaces.landlord',
        'tenant_manager'     => 'role-workspaces.tenant-manager',
        'auditor'            => 'role-workspaces.auditor',
        'secretary'          => 'role-workspaces.secretary',
        'compliance_officer' => 'role-workspaces.compliance-officer',
    ];

    /** Section view map — shared across roles. */
    private const SECTION_VIEW_MAP = [
        'invoices'           => 'role-workspaces.sections.invoices',
        'expenses'           => 'role-workspaces.sections.expenses',
        'properties'         => 'role-workspaces.sections.properties',
        'units'              => 'role-workspaces.sections.units',
        'tenants'            => 'role-workspaces.sections.tenants',
        'tickets'            => 'role-workspaces.sections.tickets',
        'maintenance'        => 'role-workspaces.sections.maintenance',
        'payments'           => 'role-workspaces.sections.payments',
        'reports'            => 'role-workspaces.sections.reports',
        'noticeboard'        => 'role-workspaces.sections.noticeboard',
        'approvals'          => 'role-workspaces.sections.approvals',
        'meetings'           => 'role-workspaces.sections.meetings',
        'resolutions'        => 'role-workspaces.sections.resolutions',
        'documents'          => 'role-workspaces.sections.documents',
        'audit-logs'         => 'role-workspaces.sections.audit-logs',
        'large-transactions' => 'role-workspaces.sections.large-transactions',
        'alerts'             => 'role-workspaces.sections.alerts',
    ];

    public function dashboard(string $role)
    {
        $config = $this->roleConfig($role);
        abort_unless($config, 404);

        $ownerUserId = $this->resolveDataOwnerUserId(getOwnerUserId());

        $base = array_merge($config, [
            'pageTitle'   => $config['label'] . ' Dashboard',
            'role'        => $role,
            'ownerUserId' => $ownerUserId,
            'navItems'    => $this->navFor($role),
        ]);

        $rich = $this->richDataFor($role, $ownerUserId);
        $view = self::VIEW_MAP[$role] ?? 'role-workspaces.director';

        return view($view, array_merge($base, $rich));
    }

    public function workbench(string $role, string $section)
    {
        $config = $this->roleConfig($role);
        abort_unless($config, 404);

        $ownerUserId = $this->resolveDataOwnerUserId(getOwnerUserId());

        $sectionView = self::SECTION_VIEW_MAP[$section] ?? null;
        abort_unless($sectionView, 404);

        $sectionData = $this->sectionDataFor($section, $ownerUserId);

        return view($sectionView, array_merge($config, [
            'pageTitle'   => $config['label'] . ' — ' . Str::title(str_replace('-', ' ', $section)),
            'role'        => $role,
            'section'     => $section,
            'ownerUserId' => $ownerUserId,
            'navItems'    => $this->navFor($role),
        ], $sectionData));
    }

    // ─── Dashboard data per role ──────────────────────────────────────────────

    private function richDataFor(string $role, ?int $ownerUserId): array
    {
        return match ($role) {
            'director'           => $this->directorData($ownerUserId),
            'finance_manager'    => $this->financeManagerData($ownerUserId),
            'landlord'           => $this->landlordData($ownerUserId),
            'tenant_manager'     => $this->tenantManagerData($ownerUserId),
            'auditor'            => $this->auditorData($ownerUserId),
            'secretary'          => $this->secretaryData($ownerUserId),
            'compliance_officer' => $this->complianceOfficerData($ownerUserId),
            default              => [],
        };
    }

    private function directorData(?int $ownerUserId): array
    {
        $totalUnits    = $this->unitCount($ownerUserId);
        $occupiedUnits = $this->occupiedCount($ownerUserId);
        $yearlyRevenue = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount');
        $yearlyExpenses = Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount');

        return [
            'totalProperties'     => Property::where('owner_user_id', $ownerUserId)->count(),
            'totalUnits'          => $totalUnits,
            'occupiedUnits'       => $occupiedUnits,
            'occupancyRate'       => $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0,
            'monthlyRevenue'      => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'yearlyRevenue'       => $yearlyRevenue,
            'netProfit'           => $yearlyRevenue - $yearlyExpenses,
            'pendingApprovals'    => FinancialApproval::where('owner_user_id', $ownerUserId)->where('status', APPROVAL_STATUS_PENDING)->count(),
            'openResolutions'     => Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_OPEN)->count(),
            'upcomingMeetings'    => GovernanceMeeting::where('owner_user_id', $ownerUserId)->where('status', MEETING_STATUS_SCHEDULED)->where('scheduled_at', '>=', now())->orderBy('scheduled_at')->limit(5)->get(),
            'recentApprovals'     => FinancialApproval::where('owner_user_id', $ownerUserId)->with('requestedBy')->orderByDesc('created_at')->limit(8)->get(),
            'months'              => $this->monthLabels(),
            'monthlyRevenueChart' => $this->monthlyRevenueArray($ownerUserId),
        ];
    }

    private function financeManagerData(?int $ownerUserId): array
    {
        return [
            'monthRevenue'       => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'monthExpenses'      => Expense::where('owner_user_id', $ownerUserId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
            'totalRevenue'       => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount'),
            'totalExpenses'      => Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount'),
            'outstandingBalance' => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->sum('amount'),
            'pendingExpenses'    => AccountantExpense::where('owner_user_id', $ownerUserId)->where('status', 0)->count(),
            'netProfit'          => (Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount')) - (Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount')),
            'recentInvoices'     => Invoice::where('owner_user_id', $ownerUserId)->with('tenant')->orderByDesc('created_at')->limit(8)->get(),
            'recentExpensesList' => Expense::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->limit(6)->get(),
            'months'             => $this->monthLabels(),
            'revenueChart'       => $this->monthlyRevenueArray($ownerUserId),
            'expenseChart'       => $this->monthlyExpenseArray($ownerUserId),
        ];
    }

    private function landlordData(?int $ownerUserId): array
    {
        $maintenanceCost = Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount');

        return [
            'totalProperties' => Property::where('owner_user_id', $ownerUserId)->count(),
            'totalUnits'      => $this->unitCount($ownerUserId),
            'totalTenants'    => Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->count(),
            'monthlyIncome'   => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'yearlyIncome'    => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount'),
            'pendingPayments' => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count(),
            'maintenanceCost' => $maintenanceCost,
            'properties'      => Property::where('owner_user_id', $ownerUserId)->with(['propertyUnits'])->orderByDesc('created_at')->limit(8)->get(),
        ];
    }

    private function tenantManagerData(?int $ownerUserId): array
    {
        $totalUnits    = $this->unitCount($ownerUserId);
        $occupiedUnits = $this->occupiedCount($ownerUserId);

        return [
            'totalTenants'   => Tenant::where('owner_user_id', $ownerUserId)->count(),
            'activeTenants'  => Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->count(),
            'vacantUnits'    => max($totalUnits - $occupiedUnits, 0),
            'totalUnits'     => $totalUnits,
            'occupiedUnits'  => $occupiedUnits,
            'expiringLeases' => Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->whereNotNull('lease_end_date')->whereBetween('lease_end_date', [now(), now()->addDays(60)])->orderBy('lease_end_date')->limit(10)->get(),
            'openTickets'    => Ticket::where('owner_user_id', $ownerUserId)->where('status', '!=', TICKET_STATUS_CLOSE)->count(),
            'pendingInvoices'=> Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count(),
            'recentTenants'  => Tenant::where('owner_user_id', $ownerUserId)->with('user')->orderByDesc('created_at')->limit(8)->get(),
        ];
    }

    private function auditorData(?int $ownerUserId): array
    {
        $actionSummary = FinancialAuditLog::where('owner_user_id', $ownerUserId)
            ->selectRaw('action, count(*) as total')
            ->groupBy('action')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return [
            'totalAuditLogs'    => FinancialAuditLog::where('owner_user_id', $ownerUserId)->count(),
            'todayLogs'         => FinancialAuditLog::where('owner_user_id', $ownerUserId)->whereDate('created_at', today())->count(),
            'weekLogs'          => FinancialAuditLog::where('owner_user_id', $ownerUserId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'totalRevenue'      => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount'),
            'totalExpenses'     => Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount'),
            'pendingApprovals'  => FinancialApproval::where('owner_user_id', $ownerUserId)->where('status', APPROVAL_STATUS_PENDING)->count(),
            'recentLogs'        => FinancialAuditLog::where('owner_user_id', $ownerUserId)->with('user')->orderByDesc('created_at')->limit(10)->get(),
            'actionSummary'     => $actionSummary,
            'largeTransactions' => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->with('tenant')->orderByDesc('amount')->limit(8)->get(),
        ];
    }

    private function secretaryData(?int $ownerUserId): array
    {
        return [
            'totalMeetings'    => GovernanceMeeting::where('owner_user_id', $ownerUserId)->count(),
            'totalResolutions' => Resolution::where('owner_user_id', $ownerUserId)->count(),
            'totalDocuments'   => GovernanceDocument::where('owner_user_id', $ownerUserId)->count(),
            'upcomingMeetings' => GovernanceMeeting::where('owner_user_id', $ownerUserId)->where('status', MEETING_STATUS_SCHEDULED)->where('scheduled_at', '>=', now())->orderBy('scheduled_at')->limit(10)->get(),
            'openResolutions'  => Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_OPEN)->orderBy('voting_closes_at')->limit(10)->get(),
            'calendarMeetings' => GovernanceMeeting::where('owner_user_id', $ownerUserId)->whereMonth('scheduled_at', now()->month)->whereYear('scheduled_at', now()->year)->get(),
        ];
    }

    private function complianceOfficerData(?int $ownerUserId): array
    {
        $openResolutionCount = Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_OPEN)->count();
        $totalDocuments      = GovernanceDocument::where('owner_user_id', $ownerUserId)->count();
        $unpaidInvoices      = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count();

        $alerts = [];
        if ($openResolutionCount > 0) {
            $alerts[] = ['type' => 'warning', 'message' => $openResolutionCount . ' open resolution(s) awaiting closure'];
        }
        if ($totalDocuments === 0) {
            $alerts[] = ['type' => 'danger', 'message' => 'Governance library is empty — upload documents'];
        }
        if ($unpaidInvoices > 5) {
            $alerts[] = ['type' => 'danger', 'message' => $unpaidInvoices . ' unpaid invoices — collections risk'];
        }

        return [
            'totalDocuments'    => $totalDocuments,
            'totalResolutions'  => Resolution::where('owner_user_id', $ownerUserId)->count(),
            'passedResolutions' => Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_PASSED)->count(),
            'failedResolutions' => Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_FAILED)->count(),
            'openResolutions'   => $openResolutionCount,
            'activeTenants'     => Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->count(),
            'complianceAlerts'  => $alerts,
            'recentDocuments'   => GovernanceDocument::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->limit(8)->get(),
            'recentAuditLogs'   => GovernanceAuditLog::where('owner_user_id', $ownerUserId)->with('user')->orderByDesc('created_at')->limit(8)->get(),
        ];
    }

    // ─── Section data ─────────────────────────────────────────────────────────

    private function sectionDataFor(string $section, ?int $ownerUserId): array
    {
        return match ($section) {
            'invoices'           => $this->sectionInvoices($ownerUserId),
            'expenses'           => $this->sectionExpenses($ownerUserId),
            'properties'         => $this->sectionProperties($ownerUserId),
            'units'              => $this->sectionUnits($ownerUserId),
            'tenants'            => $this->sectionTenants($ownerUserId),
            'tickets'            => $this->sectionTickets($ownerUserId),
            'maintenance'        => $this->sectionMaintenance($ownerUserId),
            'payments'           => $this->sectionPayments($ownerUserId),
            'reports'            => $this->sectionReports($ownerUserId),
            'noticeboard'        => $this->sectionNoticeboard($ownerUserId),
            'approvals'          => $this->sectionApprovals($ownerUserId),
            'meetings'           => $this->sectionMeetings($ownerUserId),
            'resolutions'        => $this->sectionResolutions($ownerUserId),
            'documents'          => $this->sectionDocuments($ownerUserId),
            'audit-logs'         => $this->sectionAuditLogs($ownerUserId),
            'large-transactions' => $this->sectionLargeTransactions($ownerUserId),
            'alerts'             => $this->sectionAlerts($ownerUserId),
            default              => [],
        };
    }

    private function sectionInvoices(?int $ownerUserId): array
    {
        return [
            'records'    => Invoice::where('owner_user_id', $ownerUserId)->with('tenant')->orderByDesc('created_at')->paginate(25),
            'totalPaid'  => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->sum('amount'),
            'totalUnpaid'=> Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->sum('amount'),
            'countPaid'  => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->count(),
            'countUnpaid'=> Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count(),
        ];
    }

    private function sectionExpenses(?int $ownerUserId): array
    {
        return [
            'records'     => Expense::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->paginate(25),
            'totalAmount' => Expense::where('owner_user_id', $ownerUserId)->sum('total_amount'),
            'thisYear'    => Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount'),
            'thisMonth'   => Expense::where('owner_user_id', $ownerUserId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
        ];
    }

    private function sectionProperties(?int $ownerUserId): array
    {
        return [
            'records'     => Property::where('owner_user_id', $ownerUserId)->with(['propertyUnits'])->orderByDesc('created_at')->paginate(20),
            'totalCount'  => Property::where('owner_user_id', $ownerUserId)->count(),
            'totalUnits'  => $this->unitCount($ownerUserId),
            'occupiedUnits' => $this->occupiedCount($ownerUserId),
        ];
    }

    private function sectionUnits(?int $ownerUserId): array
    {
        $units = PropertyUnit::join('properties', 'property_units.property_id', '=', 'properties.id')
            ->where('properties.owner_user_id', $ownerUserId)
            ->select('property_units.*', 'properties.name as property_name')
            ->with('activeTenant')
            ->orderBy('properties.name')
            ->paginate(25);

        return [
            'records'      => $units,
            'totalUnits'   => $this->unitCount($ownerUserId),
            'occupiedUnits'=> $this->occupiedCount($ownerUserId),
            'vacantUnits'  => max($this->unitCount($ownerUserId) - $this->occupiedCount($ownerUserId), 0),
        ];
    }

    private function sectionTenants(?int $ownerUserId): array
    {
        return [
            'records'      => Tenant::where('owner_user_id', $ownerUserId)->with('user')->orderByDesc('created_at')->paginate(25),
            'activeCount'  => Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->count(),
            'totalCount'   => Tenant::where('owner_user_id', $ownerUserId)->count(),
            'expiringCount'=> Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->whereNotNull('lease_end_date')->whereBetween('lease_end_date', [now(), now()->addDays(60)])->count(),
        ];
    }

    private function sectionTickets(?int $ownerUserId): array
    {
        return [
            'records'      => Ticket::where('owner_user_id', $ownerUserId)->with(['user', 'property', 'topic'])->orderByDesc('created_at')->paginate(25),
            'openCount'    => Ticket::where('owner_user_id', $ownerUserId)->where('status', '!=', TICKET_STATUS_CLOSE)->count(),
            'closedCount'  => Ticket::where('owner_user_id', $ownerUserId)->where('status', TICKET_STATUS_CLOSE)->count(),
        ];
    }

    private function sectionMaintenance(?int $ownerUserId): array
    {
        $records = MaintenanceRequest::join('properties', 'maintenance_requests.property_id', '=', 'properties.id')
            ->where('properties.owner_user_id', $ownerUserId)
            ->select('maintenance_requests.*', 'properties.name as property_name')
            ->with('fileAttachFile')
            ->orderByDesc('maintenance_requests.created_at')
            ->paginate(25);

        return [
            'records'        => $records,
            'pendingCount'   => MaintenanceRequest::join('properties', 'maintenance_requests.property_id', '=', 'properties.id')->where('properties.owner_user_id', $ownerUserId)->where('maintenance_requests.status', MAINTENANCE_REQUEST_STATUS_PENDING)->count(),
            'totalCost'      => MaintenanceRequest::join('properties', 'maintenance_requests.property_id', '=', 'properties.id')->where('properties.owner_user_id', $ownerUserId)->sum('maintenance_requests.amount'),
        ];
    }

    private function sectionPayments(?int $ownerUserId): array
    {
        return [
            'records'     => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->with('tenant')->orderByDesc('created_at')->paginate(25),
            'totalAmount' => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->sum('amount'),
            'thisMonth'   => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'thisYear'    => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount'),
        ];
    }

    private function sectionReports(?int $ownerUserId): array
    {
        $months       = $this->monthLabels();
        $revenueChart = $this->monthlyRevenueArray($ownerUserId);
        $expenseChart = $this->monthlyExpenseArray($ownerUserId);
        $totalRevenue = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereYear('created_at', now()->year)->sum('amount');
        $totalExpenses= Expense::where('owner_user_id', $ownerUserId)->whereYear('created_at', now()->year)->sum('total_amount');

        return [
            'months'       => $months,
            'revenueChart' => $revenueChart,
            'expenseChart' => $expenseChart,
            'totalRevenue' => $totalRevenue,
            'totalExpenses'=> $totalExpenses,
            'netProfit'    => $totalRevenue - $totalExpenses,
            'monthRevenue' => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'monthExpenses'=> Expense::where('owner_user_id', $ownerUserId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
        ];
    }

    private function sectionNoticeboard(?int $ownerUserId): array
    {
        return [
            'records'    => NoticeBoard::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->paginate(25),
            'totalCount' => NoticeBoard::where('owner_user_id', $ownerUserId)->count(),
        ];
    }

    private function sectionApprovals(?int $ownerUserId): array
    {
        return [
            'records'       => FinancialApproval::where('owner_user_id', $ownerUserId)->with('requestedBy')->orderByDesc('created_at')->paginate(25),
            'pendingCount'  => FinancialApproval::where('owner_user_id', $ownerUserId)->where('status', APPROVAL_STATUS_PENDING)->count(),
            'approvedCount' => FinancialApproval::where('owner_user_id', $ownerUserId)->where('status', APPROVAL_STATUS_APPROVED)->count(),
            'rejectedCount' => FinancialApproval::where('owner_user_id', $ownerUserId)->where('status', APPROVAL_STATUS_REJECTED)->count(),
        ];
    }

    private function sectionMeetings(?int $ownerUserId): array
    {
        return [
            'records'        => GovernanceMeeting::where('owner_user_id', $ownerUserId)->orderByDesc('scheduled_at')->paginate(25),
            'upcomingCount'  => GovernanceMeeting::where('owner_user_id', $ownerUserId)->where('status', MEETING_STATUS_SCHEDULED)->where('scheduled_at', '>=', now())->count(),
            'totalCount'     => GovernanceMeeting::where('owner_user_id', $ownerUserId)->count(),
        ];
    }

    private function sectionResolutions(?int $ownerUserId): array
    {
        return [
            'records'    => Resolution::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->paginate(25),
            'openCount'  => Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_OPEN)->count(),
            'passedCount'=> Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_PASSED)->count(),
            'failedCount'=> Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_FAILED)->count(),
        ];
    }

    private function sectionDocuments(?int $ownerUserId): array
    {
        return [
            'records'    => GovernanceDocument::where('owner_user_id', $ownerUserId)->orderByDesc('created_at')->paginate(25),
            'totalCount' => GovernanceDocument::where('owner_user_id', $ownerUserId)->count(),
        ];
    }

    private function sectionAuditLogs(?int $ownerUserId): array
    {
        return [
            'records'    => FinancialAuditLog::where('owner_user_id', $ownerUserId)->with('user')->orderByDesc('created_at')->paginate(30),
            'totalCount' => FinancialAuditLog::where('owner_user_id', $ownerUserId)->count(),
            'todayCount' => FinancialAuditLog::where('owner_user_id', $ownerUserId)->whereDate('created_at', today())->count(),
        ];
    }

    private function sectionLargeTransactions(?int $ownerUserId): array
    {
        return [
            'records'    => Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->with('tenant')->orderByDesc('amount')->paginate(25),
            'totalAmount'=> Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_PAID)->sum('amount'),
        ];
    }

    private function sectionAlerts(?int $ownerUserId): array
    {
        $openResolutions = Resolution::where('owner_user_id', $ownerUserId)->where('status', RESOLUTION_STATUS_OPEN)->count();
        $documents       = GovernanceDocument::where('owner_user_id', $ownerUserId)->count();
        $unpaid          = Invoice::where('owner_user_id', $ownerUserId)->where('status', INVOICE_STATUS_UNPAID)->count();
        $expiringLeases  = Tenant::where('owner_user_id', $ownerUserId)->where('status', TENANT_STATUS_ACTIVE)->whereNotNull('lease_end_date')->whereBetween('lease_end_date', [now(), now()->addDays(30)])->count();

        $alerts = [];
        if ($openResolutions > 0) {
            $alerts[] = ['type' => 'warning', 'message' => $openResolutions . ' open resolution(s) awaiting closure', 'icon' => 'ri-discuss-line'];
        }
        if ($documents === 0) {
            $alerts[] = ['type' => 'danger', 'message' => 'Governance library is empty — upload governance documents', 'icon' => 'ri-file-warning-line'];
        }
        if ($unpaid > 5) {
            $alerts[] = ['type' => 'danger', 'message' => $unpaid . ' unpaid invoices — collections risk escalating', 'icon' => 'ri-alert-line'];
        }
        if ($expiringLeases > 0) {
            $alerts[] = ['type' => 'warning', 'message' => $expiringLeases . ' lease(s) expiring within 30 days', 'icon' => 'ri-calendar-close-line'];
        }
        if (empty($alerts)) {
            $alerts[] = ['type' => 'success', 'message' => 'No active compliance alerts — all governance controls are in order', 'icon' => 'ri-shield-check-line'];
        }

        return ['alerts' => $alerts];
    }

    // ─── Config ──────────────────────────────────────────────────────────────

    private function roleConfig(string $role): ?array
    {
        $roles = [
            'director' => [
                'label'   => 'Director',
                'eyebrow' => 'Executive command center',
                'icon'    => 'ri-briefcase-4-line',
                'tone'    => 'role-tone-director',
                'summary' => 'Track strategic performance, board decisions, approvals, meetings, and governance risk from one executive workspace.',
            ],
            'finance_manager' => [
                'label'   => 'Finance Manager',
                'eyebrow' => 'Financial control room',
                'icon'    => 'ri-money-dollar-box-line',
                'tone'    => 'role-tone-finance',
                'summary' => 'Monitor revenue, expenses, outstanding balances, rent collections, and finance workflows that need action.',
            ],
            'landlord' => [
                'label'   => 'Landlord',
                'eyebrow' => 'Property performance hub',
                'icon'    => 'ri-home-8-line',
                'tone'    => 'role-tone-landlord',
                'summary' => 'See property income, occupancy, maintenance exposure, payments, units, and portfolio health.',
            ],
            'tenant_manager' => [
                'label'   => 'Tenant Manager',
                'eyebrow' => 'Tenant operations desk',
                'icon'    => 'ri-team-line',
                'tone'    => 'role-tone-tenant',
                'summary' => 'Manage tenant activity, vacancies, lease follow-ups, support tickets, notices, and onboarding work.',
            ],
            'auditor' => [
                'label'   => 'Auditor',
                'eyebrow' => 'Audit and assurance workspace',
                'icon'    => 'ri-eye-line',
                'tone'    => 'role-tone-auditor',
                'summary' => 'Review audit logs, large transactions, anomalies, approvals, and financial accountability trails.',
            ],
            'compliance_officer' => [
                'label'   => 'Compliance Officer',
                'eyebrow' => 'Governance risk console',
                'icon'    => 'ri-file-shield-2-line',
                'tone'    => 'role-tone-compliance',
                'summary' => 'Track governance documents, resolutions, compliance alerts, missing controls, and policy risks.',
            ],
            'secretary' => [
                'label'   => 'Secretary',
                'eyebrow' => 'Board administration desk',
                'icon'    => 'ri-calendar-todo-line',
                'tone'    => 'role-tone-secretary',
                'summary' => 'Coordinate meetings, minutes, governance documents, resolutions, and shareholder communications.',
            ],
        ];

        return $roles[$role] ?? null;
    }

    // ─── Nav & actions ───────────────────────────────────────────────────────

    private function resolveDataOwnerUserId(?int $ownerUserId): ?int
    {
        if (!$ownerUserId) {
            return null;
        }

        if (Property::where('owner_user_id', $ownerUserId)->exists()) {
            return $ownerUserId;
        }

        $scopedUser = User::find($ownerUserId);
        if (!$scopedUser || (int) $scopedUser->role !== USER_ROLE_ADMIN) {
            return $ownerUserId;
        }

        $propertyOwnerIds = Property::query()
            ->whereNotNull('owner_user_id')
            ->distinct()
            ->pluck('owner_user_id');

        return $propertyOwnerIds->count() === 1 ? (int) $propertyOwnerIds->first() : $ownerUserId;
    }

    private function actionsFor(string $role): array
    {
        $wb = fn(string $section) => route('role.workbench', [$role, $section]);

        $actions = [
            'director' => [
                ['label' => 'Approvals',       'icon' => 'ri-check-double-line',    'url' => $wb('approvals')],
                ['label' => 'Financial Reports','icon' => 'ri-bar-chart-box-line',   'url' => $wb('reports')],
                ['label' => 'Board Calendar',  'icon' => 'ri-calendar-event-line',  'url' => $wb('meetings')],
                ['label' => 'Resolutions',     'icon' => 'ri-discuss-line',         'url' => $wb('resolutions')],
            ],
            'finance_manager' => [
                ['label' => 'Invoices',          'icon' => 'ri-file-list-3-line',    'url' => $wb('invoices')],
                ['label' => 'Expenses',          'icon' => 'ri-receipt-line',        'url' => $wb('expenses')],
                ['label' => 'Financial Reports', 'icon' => 'ri-bar-chart-box-line',  'url' => $wb('reports')],
                ['label' => 'Pending Approvals', 'icon' => 'ri-hourglass-line',      'url' => $wb('approvals')],
            ],
            'landlord' => [
                ['label' => 'Properties', 'icon' => 'ri-building-line',     'url' => $wb('properties')],
                ['label' => 'Units',      'icon' => 'ri-home-4-line',       'url' => $wb('units')],
                ['label' => 'Maintenance','icon' => 'ri-tools-line',        'url' => $wb('maintenance')],
                ['label' => 'Payments',   'icon' => 'ri-bank-card-line',    'url' => $wb('payments')],
            ],
            'tenant_manager' => [
                ['label' => 'All Tenants',  'icon' => 'ri-user-3-line',             'url' => $wb('tenants')],
                ['label' => 'Tickets',      'icon' => 'ri-customer-service-2-line', 'url' => $wb('tickets')],
                ['label' => 'Notice Board', 'icon' => 'ri-megaphone-line',          'url' => $wb('noticeboard')],
                ['label' => 'Invoices',     'icon' => 'ri-file-list-3-line',        'url' => $wb('invoices')],
            ],
            'auditor' => [
                ['label' => 'Audit Trail',       'icon' => 'ri-shield-check-line',  'url' => $wb('audit-logs')],
                ['label' => 'Expense Report',    'icon' => 'ri-receipt-line',       'url' => $wb('reports')],
                ['label' => 'Income Report',     'icon' => 'ri-line-chart-line',    'url' => $wb('reports')],
                ['label' => 'Large Transactions','icon' => 'ri-search-eye-line',    'url' => $wb('large-transactions')],
            ],
            'compliance_officer' => [
                ['label' => 'Governance Docs',   'icon' => 'ri-file-shield-2-line', 'url' => $wb('documents')],
                ['label' => 'Resolutions',       'icon' => 'ri-discuss-line',       'url' => $wb('resolutions')],
                ['label' => 'Compliance Alerts', 'icon' => 'ri-alert-line',         'url' => $wb('alerts')],
                ['label' => 'Audit Trail',       'icon' => 'ri-shield-check-line',  'url' => $wb('audit-logs')],
            ],
            'secretary' => [
                ['label' => 'Meeting Desk', 'icon' => 'ri-calendar-todo-line', 'url' => $wb('meetings')],
                ['label' => 'Documents',    'icon' => 'ri-folder-2-line',      'url' => $wb('documents')],
                ['label' => 'Resolutions',  'icon' => 'ri-discuss-line',       'url' => $wb('resolutions')],
                ['label' => 'Notice Board', 'icon' => 'ri-megaphone-line',     'url' => $wb('noticeboard')],
            ],
        ];

        return $actions[$role] ?? [];
    }

    private function navFor(string $role): array
    {
        $dashUrl = route('role.' . str_replace('_', '-', $role) . '.dashboard');
        return array_merge(
            [['label' => 'Dashboard', 'icon' => 'ri-dashboard-line', 'url' => $dashUrl]],
            $this->actionsFor($role)
        );
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function unitCount(?int $ownerUserId): int
    {
        return PropertyUnit::join('properties', 'property_units.property_id', '=', 'properties.id')
            ->where('properties.owner_user_id', $ownerUserId)
            ->count();
    }

    private function occupiedCount(?int $ownerUserId): int
    {
        return Tenant::where('owner_user_id', $ownerUserId)
            ->where('status', TENANT_STATUS_ACTIVE)
            ->whereNotNull('unit_id')
            ->distinct('unit_id')
            ->count('unit_id');
    }

    private function monthLabels(): array
    {
        return ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    }

    private function monthlyRevenueArray(?int $ownerUserId): array
    {
        $base = Invoice::where('owner_user_id', $ownerUserId)
            ->where('status', INVOICE_STATUS_PAID)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        return array_map(fn($m) => (float) ($base[$m] ?? 0), range(1, 12));
    }

    private function monthlyExpenseArray(?int $ownerUserId): array
    {
        $base = Expense::where('owner_user_id', $ownerUserId)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        return array_map(fn($m) => (float) ($base[$m] ?? 0), range(1, 12));
    }

    private function approvalStatusLabel($status): string
    {
        return match ((int) $status) {
            APPROVAL_STATUS_PENDING  => 'Pending',
            APPROVAL_STATUS_APPROVED => 'Approved',
            APPROVAL_STATUS_REJECTED => 'Rejected',
            APPROVAL_STATUS_EXPIRED  => 'Expired',
            default                  => 'Unknown',
        };
    }

    private function approvalStatusBadge($status): string
    {
        return match ((int) $status) {
            APPROVAL_STATUS_PENDING  => 'bg-warning',
            APPROVAL_STATUS_APPROVED => 'bg-success',
            APPROVAL_STATUS_REJECTED => 'bg-danger',
            APPROVAL_STATUS_EXPIRED  => 'bg-secondary',
            default                  => 'bg-secondary',
        };
    }

    private function resolutionStatus($status): string
    {
        return match ((int) $status) {
            RESOLUTION_STATUS_DRAFT  => 'Draft',
            RESOLUTION_STATUS_OPEN   => 'Open',
            RESOLUTION_STATUS_CLOSED => 'Closed',
            RESOLUTION_STATUS_PASSED => 'Passed',
            RESOLUTION_STATUS_FAILED => 'Failed',
            default                  => 'Unknown',
        };
    }
}
