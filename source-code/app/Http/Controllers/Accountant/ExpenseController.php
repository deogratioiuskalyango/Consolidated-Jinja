<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AccountantExpense;
use App\Models\FinancialAuditLog;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    use ResponseTrait;

    /**
     * Expense category labels keyed by constant value.
     */
    private function expenseCategories(): array
    {
        return [
            EXPENSE_CAT_MAINTENANCE => 'Maintenance',
            EXPENSE_CAT_UTILITIES   => 'Utilities',
            EXPENSE_CAT_CONTRACTOR  => 'Contractor',
            EXPENSE_CAT_SALARY      => 'Salary',
            EXPENSE_CAT_INSURANCE   => 'Insurance',
            EXPENSE_CAT_LEGAL       => 'Legal',
            EXPENSE_CAT_MARKETING   => 'Marketing',
            EXPENSE_CAT_OTHER       => 'Other',
        ];
    }

    public function index()
    {
        $ownerUserId = auth()->user()->accountant->owner_user_id;

        $expenses = AccountantExpense::where('owner_user_id', $ownerUserId)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('accountant.expenses.index', [
            'expenses'          => $expenses,
            'expenseCategories' => $this->expenseCategories(),
            'pageTitle'         => 'Expenses',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:191',
            'category'      => 'required|integer|between:1,8',
            'amount'        => 'required|numeric|min:0.01',
            'expense_date'  => 'required|date',
            'description'   => 'nullable|string',
            'receipt_image' => 'nullable|file|max:5120',
        ]);

        $accountant  = auth()->user()->accountant;
        $ownerUserId = $accountant->owner_user_id;

        DB::beginTransaction();
        try {
            // Generate reference number
            $count     = AccountantExpense::where('owner_user_id', $ownerUserId)->count();
            $reference = 'EXP-' . date('Ym') . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            $expense = AccountantExpense::create([
                'accountant_id'  => $accountant->id,
                'owner_user_id'  => $ownerUserId,
                'title'          => $request->title,
                'category'       => $request->category,
                'amount'         => $request->amount,
                'expense_date'   => $request->expense_date,
                'description'    => $request->description,
                'reference'      => $reference,
                'status'         => EXPENSE_STATUS_PENDING,
            ]);

            // Handle receipt image upload
            if ($request->hasFile('receipt_image')) {
                $existFile = \App\Models\FileManager::where('origin_type', 'App\Models\AccountantExpense')
                    ->where('origin_id', $expense->id)->first();
                if ($existFile) {
                    $existFile->removeFile();
                    $upload = $existFile->updateUpload($existFile->id, 'AccountantExpense', $request->receipt_image);
                } else {
                    $newFile = new \App\Models\FileManager();
                    $upload  = $newFile->upload('AccountantExpense', $request->receipt_image);
                }
                if (!empty($upload['status'])) {
                    $upload['file']->origin_id   = $expense->id;
                    $upload['file']->origin_type = 'App\Models\AccountantExpense';
                    $upload['file']->save();
                }
            }

            FinancialAuditLog::record(
                AUDIT_EXPENSE_CREATED,
                $accountant,
                [
                    'before' => [],
                    'after'  => ['reference' => $reference, 'amount' => $request->amount, 'category' => $request->category],
                ],
                'Expense created — ' . $reference
            );

            DB::commit();
            return $this->success([], 'Expense recorded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function show(AccountantExpense $expense)
    {
        $expense->load('accountant');

        return view('accountant.expenses.show', [
            'expense'           => $expense,
            'expenseCategories' => $this->expenseCategories(),
            'pageTitle'         => 'Expense Details',
        ]);
    }

    public function approve(AccountantExpense $expense)
    {
        $accountant = auth()->user()->accountant;

        DB::beginTransaction();
        try {
            $expense->update([
                'status'      => EXPENSE_STATUS_APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            FinancialAuditLog::record(
                AUDIT_EXPENSE_APPROVED,
                $accountant,
                [
                    'before' => ['status' => EXPENSE_STATUS_PENDING],
                    'after'  => ['status' => EXPENSE_STATUS_APPROVED],
                ],
                'Expense approved — ' . $expense->reference
            );

            DB::commit();
            return $this->success([], 'Expense approved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function reject(AccountantExpense $expense)
    {
        $accountant = auth()->user()->accountant;

        DB::beginTransaction();
        try {
            $expense->update([
                'status' => EXPENSE_STATUS_REJECTED,
            ]);

            FinancialAuditLog::record(
                AUDIT_EXPENSE_REJECTED,
                $accountant,
                [
                    'before' => ['status' => EXPENSE_STATUS_PENDING],
                    'after'  => ['status' => EXPENSE_STATUS_REJECTED],
                ],
                'Expense rejected — ' . $expense->reference
            );

            DB::commit();
            return $this->success([], 'Expense rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function destroy(AccountantExpense $expense)
    {
        if ($expense->status !== EXPENSE_STATUS_PENDING) {
            return $this->error([], 'Cannot delete processed expense.');
        }

        DB::beginTransaction();
        try {
            $expense->delete();
            DB::commit();
            return $this->success([], 'Expense deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }
}
