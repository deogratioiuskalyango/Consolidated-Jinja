<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\FinancialReport;
use App\Models\FinancialAuditLog;
use App\Models\RentCollection;
use App\Models\AccountantExpense;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ResponseTrait;

    /**
     * Report type labels keyed by constant value.
     */
    private function reportTypes(): array
    {
        return [
            REPORT_TYPE_WEEKLY    => 'Weekly',
            REPORT_TYPE_MONTHLY   => 'Monthly',
            REPORT_TYPE_QUARTERLY => 'Quarterly',
            REPORT_TYPE_ANNUAL    => 'Annual',
            REPORT_TYPE_CUSTOM    => 'Custom',
        ];
    }

    public function index()
    {
        $ownerUserId = auth()->user()->accountant->owner_user_id;

        $reports = FinancialReport::where('owner_user_id', $ownerUserId)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('accountant.reports.index', [
            'reports'     => $reports,
            'reportTypes' => $this->reportTypes(),
            'pageTitle'   => 'Financial Reports',
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type'  => 'required|integer|between:1,5',
            'title'        => 'required|string|max:191',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'notes'        => 'nullable|string',
        ]);

        $accountant  = auth()->user()->accountant;
        $ownerUserId = $accountant->owner_user_id;

        DB::beginTransaction();
        try {
            $start = $request->period_start;
            $end   = $request->period_end;

            // Total confirmed collections in period
            $totalCollections = RentCollection::where('owner_user_id', $ownerUserId)
                ->where('status', COLLECTION_STATUS_CONFIRMED)
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount');

            // Total approved expenses in period
            $totalExpenses = AccountantExpense::where('owner_user_id', $ownerUserId)
                ->where('status', EXPENSE_STATUS_APPROVED)
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            $netIncome = $totalCollections - $totalExpenses;

            // Collections grouped by payment method
            $collectionsByMethod = RentCollection::where('owner_user_id', $ownerUserId)
                ->where('status', COLLECTION_STATUS_CONFIRMED)
                ->whereBetween('payment_date', [$start, $end])
                ->select('payment_method', DB::raw('SUM(amount) as total'))
                ->groupBy('payment_method')
                ->get()
                ->pluck('total', 'payment_method')
                ->toArray();

            // Collections grouped by property
            $collectionsByProperty = RentCollection::where('owner_user_id', $ownerUserId)
                ->where('status', COLLECTION_STATUS_CONFIRMED)
                ->whereBetween('payment_date', [$start, $end])
                ->select('property_id', DB::raw('SUM(amount) as total'))
                ->groupBy('property_id')
                ->get()
                ->pluck('total', 'property_id')
                ->toArray();

            // Expenses grouped by category
            $expenseByCategory = AccountantExpense::where('owner_user_id', $ownerUserId)
                ->where('status', EXPENSE_STATUS_APPROVED)
                ->whereBetween('expense_date', [$start, $end])
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->get()
                ->pluck('total', 'category')
                ->toArray();

            // Counts
            $collectionCount = RentCollection::where('owner_user_id', $ownerUserId)
                ->where('status', COLLECTION_STATUS_CONFIRMED)
                ->whereBetween('payment_date', [$start, $end])
                ->count();

            $expenseCount = AccountantExpense::where('owner_user_id', $ownerUserId)
                ->where('status', EXPENSE_STATUS_APPROVED)
                ->whereBetween('expense_date', [$start, $end])
                ->count();

            $reportData = [
                'total_collections'     => $totalCollections,
                'total_expenses'        => $totalExpenses,
                'net_income'            => $netIncome,
                'collections_by_method' => $collectionsByMethod,
                'collections_by_property' => $collectionsByProperty,
                'expense_by_category'   => $expenseByCategory,
                'collection_count'      => $collectionCount,
                'expense_count'         => $expenseCount,
            ];

            $report = FinancialReport::create([
                'owner_user_id' => $ownerUserId,
                'accountant_id' => $accountant->id,
                'report_type'   => $request->report_type,
                'title'         => $request->title,
                'period_start'  => $start,
                'period_end'    => $end,
                'notes'         => $request->notes,
                'report_data'   => $reportData,
                'status'        => REPORT_STATUS_DRAFT,
            ]);

            FinancialAuditLog::record(
                AUDIT_REPORT_GENERATED,
                $accountant,
                [
                    'before' => [],
                    'after'  => ['report_id' => $report->id, 'title' => $report->title, 'period' => "$start to $end"],
                ],
                'Financial report generated: ' . $report->title
            );

            DB::commit();
            return $this->success(['id' => $report->id], 'Report generated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function show(FinancialReport $report)
    {
        return view('accountant.reports.show', [
            'report'     => $report,
            'reportTypes'=> $this->reportTypes(),
            'pageTitle'  => $report->title,
        ]);
    }

    public function print(FinancialReport $report)
    {
        $accountant = auth()->user()->accountant;

        FinancialAuditLog::record(
            AUDIT_REPORT_VIEWED,
            $accountant,
            [
                'before' => [],
                'after'  => ['report_id' => $report->id],
            ],
            'Report printed/viewed: ' . $report->title
        );

        return view('accountant.reports.print', [
            'report'     => $report,
            'reportTypes'=> $this->reportTypes(),
        ]);
    }

    public function exportCsv(FinancialReport $report)
    {
        $data       = $report->report_data;
        $reportTypes= $this->reportTypes();

        $rows   = [];
        $rows[] = ['Financial Report: ' . $report->title];
        $rows[] = ['Period', $report->period_start . ' to ' . $report->period_end];
        $rows[] = ['Type', $reportTypes[$report->report_type] ?? $report->report_type];
        $rows[] = ['Generated At', $report->created_at->toDateTimeString()];
        $rows[] = [];

        // Summary section
        $rows[] = ['Summary'];
        $rows[] = ['Total Collections', $data['total_collections'] ?? 0];
        $rows[] = ['Total Expenses', $data['total_expenses'] ?? 0];
        $rows[] = ['Net Income', $data['net_income'] ?? 0];
        $rows[] = ['Collection Count', $data['collection_count'] ?? 0];
        $rows[] = ['Expense Count', $data['expense_count'] ?? 0];
        $rows[] = [];

        // Collections by method
        $rows[] = ['Collections by Payment Method'];
        $rows[] = ['Payment Method', 'Total'];
        $methodLabels = [
            PAYMENT_METHOD_CASH         => 'Cash',
            PAYMENT_METHOD_BANK         => 'Bank Transfer',
            PAYMENT_METHOD_MTN_MOMO     => 'MTN Mobile Money',
            PAYMENT_METHOD_AIRTEL_MONEY => 'Airtel Money',
            PAYMENT_METHOD_CHEQUE       => 'Cheque',
            PAYMENT_METHOD_CARD         => 'Card',
        ];
        foreach (($data['collections_by_method'] ?? []) as $method => $total) {
            $rows[] = [$methodLabels[$method] ?? $method, $total];
        }
        $rows[] = [];

        // Expenses by category
        $rows[] = ['Expenses by Category'];
        $rows[] = ['Category', 'Total'];
        $catLabels = [
            EXPENSE_CAT_MAINTENANCE => 'Maintenance',
            EXPENSE_CAT_UTILITIES   => 'Utilities',
            EXPENSE_CAT_CONTRACTOR  => 'Contractor',
            EXPENSE_CAT_SALARY      => 'Salary',
            EXPENSE_CAT_INSURANCE   => 'Insurance',
            EXPENSE_CAT_LEGAL       => 'Legal',
            EXPENSE_CAT_MARKETING   => 'Marketing',
            EXPENSE_CAT_OTHER       => 'Other',
        ];
        foreach (($data['expense_by_category'] ?? []) as $cat => $total) {
            $rows[] = [$catLabels[$cat] ?? $cat, $total];
        }

        // Build CSV string
        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($cell) => '"' . str_replace('"', '""', $cell) . '"', $row)) . "\n";
        }

        $filename = 'report-' . $report->id . '-' . date('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function shareToShareholders(FinancialReport $report)
    {
        DB::beginTransaction();
        try {
            $report->update([
                'shared_with_shareholders' => true,
                'status'                   => REPORT_STATUS_PUBLISHED,
            ]);

            DB::commit();
            return $this->success([], 'Report shared with shareholders.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function destroy(FinancialReport $report)
    {
        DB::beginTransaction();
        try {
            $report->delete();
            DB::commit();
            return $this->success([], 'Report deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }
}
