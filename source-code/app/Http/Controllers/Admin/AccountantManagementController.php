<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Accountant;
use App\Models\FinancialAuditLog;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use DataTables;

class AccountantManagementController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $data['pageTitle'] = __('Accountants');
        return view('admin.accountants.index', $data);
    }

    public function getData()
    {
        $accountants = Accountant::with('user')->select('accountants.*');

        return DataTables::of($accountants)
            ->addIndexColumn()
            ->addColumn('name', fn($a) => trim(($a->user->first_name ?? '') . ' ' . ($a->user->last_name ?? '')) ?: '-')
            ->addColumn('email', fn($a) => $a->user->email ?? '-')
            ->addColumn('phone', fn($a) => $a->user->contact_number ?? '-')
            ->addColumn('status_badge', fn($a) => $a->status == ACCOUNTANT_STATUS_ACTIVE
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Suspended</span>')
            ->addColumn('action', function ($a) {
                $suspendBtn = $a->status == ACCOUNTANT_STATUS_ACTIVE
                    ? '<button class="btn btn-sm btn-outline-warning acc-suspend-btn" data-id="' . $a->id . '" title="Suspend"><i class="ri-pause-circle-line"></i></button>'
                    : '<button class="btn btn-sm btn-outline-success acc-reactivate-btn" data-id="' . $a->id . '" title="Reactivate"><i class="ri-play-circle-line"></i></button>';

                return '<div class="d-inline-flex gap-1">'
                    . '<button class="btn btn-sm btn-outline-primary acc-edit-btn" data-id="' . $a->id . '" title="Edit"><i class="ri-pencil-line"></i></button>'
                    . $suspendBtn
                    . '<button class="btn btn-sm btn-outline-danger acc-delete-btn" data-id="' . $a->id . '" title="Delete"><i class="ri-delete-bin-line"></i></button>'
                    . '</div>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|max:191',
            'last_name'      => 'required|max:191',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'nullable|max:50',
            'designation'    => 'nullable|max:191',
            'license_number' => 'nullable|max:191',
        ]);

        DB::beginTransaction();
        try {
            $password = \Str::random(10);

            $user = User::create([
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'email'          => $request->email,
                'contact_number' => $request->phone,
                'password'       => Hash::make($password),
                'role'           => USER_ROLE_ACCOUNTANT,
                'status'         => USER_STATUS_ACTIVE,
            ]);

            $accountantId = 'ACC-' . str_pad(Accountant::count() + 1, 4, '0', STR_PAD_LEFT);

            Accountant::create([
                'user_id'               => $user->id,
                'owner_user_id'         => auth()->id(),
                'accountant_id'         => $accountantId,
                'designation'           => $request->designation,
                'license_number'        => $request->license_number,
                'status'                => ACCOUNTANT_STATUS_ACTIVE,
                'force_password_change' => true,
            ]);

            try {
                \Mail::mailer('failover')->to($user->email)->send(new \App\Mail\AccountantCredentialsMail($user, $password));
            } catch (\Throwable $e) {
                \Log::warning('AccountantCredentialsMail failed for ' . $user->email . ': ' . $e->getMessage());
            }

            DB::commit();
            return $this->success([], __('Accountant created. Credentials sent to email.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function edit(Accountant $accountant)
    {
        return response()->json([
            'status' => 200,
            'data'   => [
                'id'             => $accountant->id,
                'first_name'     => $accountant->user->first_name     ?? '',
                'last_name'      => $accountant->user->last_name      ?? '',
                'email'          => $accountant->user->email          ?? '',
                'phone'          => $accountant->user->contact_number ?? '',
                'designation'    => $accountant->designation,
                'license_number' => $accountant->license_number,
            ],
        ]);
    }

    public function update(Request $request, Accountant $accountant)
    {
        $request->validate([
            'first_name'     => 'required|max:191',
            'last_name'      => 'required|max:191',
            'email'          => 'required|email|unique:users,email,' . $accountant->user_id,
            'phone'          => 'nullable|max:50',
            'designation'    => 'nullable|max:191',
            'license_number' => 'nullable|max:191',
        ]);

        DB::beginTransaction();
        try {
            $accountant->user->update([
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'email'          => $request->email,
                'contact_number' => $request->phone,
            ]);

            $accountant->update([
                'designation'    => $request->designation,
                'license_number' => $request->license_number,
            ]);

            DB::commit();
            return $this->success([], __('Accountant updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function suspend(Accountant $accountant)
    {
        $accountant->update(['status' => ACCOUNTANT_STATUS_SUSPENDED]);

        FinancialAuditLog::record(
            'suspend_accountant',
            $accountant->owner_user_id ?? auth()->id(),
            $accountant,
            [],
            [],
            'Accountant suspended'
        );

        return $this->success([], __('Accountant suspended.'));
    }

    public function reactivate(Accountant $accountant)
    {
        $accountant->update(['status' => ACCOUNTANT_STATUS_ACTIVE]);

        FinancialAuditLog::record(
            'reactivate_accountant',
            $accountant->owner_user_id ?? auth()->id(),
            $accountant,
            [],
            [],
            'Accountant reactivated'
        );

        return $this->success([], __('Accountant reactivated.'));
    }

    public function destroy(Accountant $accountant)
    {
        DB::beginTransaction();
        try {
            FinancialAuditLog::record(
                'delete_accountant',
                $accountant->owner_user_id ?? auth()->id(),
                $accountant,
                [],
                [],
                'Accountant account deleted'
            );

            $accountant->user->delete();
            $accountant->delete();

            DB::commit();
            return $this->success([], __('Accountant deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }
}
