<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountDeletionRequestController extends Controller
{
    use ResponseTrait;

    /**
     * List all deletion requests (most recent first).
     */
    public function index(Request $request)
    {
        $data['pageTitle']   = __('Account Deletion Requests');
        $data['activeStatus'] = $request->get('status', 'pending');

        $query = AccountDeletionRequest::with(['user', 'reviewer'])
            ->latest();

        if ($data['activeStatus'] === 'pending') {
            $query->where('status', DELETION_REQUEST_PENDING);
        } elseif ($data['activeStatus'] === 'approved') {
            $query->where('status', DELETION_REQUEST_APPROVED);
        } elseif ($data['activeStatus'] === 'rejected') {
            $query->where('status', DELETION_REQUEST_REJECTED);
        }

        $data['requests']       = $query->paginate(15)->withQueryString();
        $data['pendingCount']   = AccountDeletionRequest::pending()->count();
        $data['approvedCount']  = AccountDeletionRequest::approved()->count();
        $data['rejectedCount']  = AccountDeletionRequest::rejected()->count();

        return view('admin.deletion-requests.index', $data);
    }

    /**
     * Approve a deletion request — permanently delete the user account.
     */
    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $deletionRequest = AccountDeletionRequest::with('user')->findOrFail($id);

            if ($deletionRequest->status !== DELETION_REQUEST_PENDING) {
                throw new Exception(__('This request has already been reviewed.'));
            }

            $user = User::findOrFail($deletionRequest->user_id);

            // Mark the deletion request as approved
            $deletionRequest->status      = DELETION_REQUEST_APPROVED;
            $deletionRequest->reviewed_by = Auth::id();
            $deletionRequest->reviewed_at = now();
            $deletionRequest->admin_note  = $request->admin_note;
            $deletionRequest->save();

            // Permanently delete the user (soft-delete column exists, but we hard-delete)
            $user->status = USER_STATUS_DELETED;
            $user->save();

            DB::commit();
            return $this->success([], __('Deletion request approved. The account has been deleted.'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * Reject a deletion request — restore the user's account to active.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $deletionRequest = AccountDeletionRequest::with('user')->findOrFail($id);

            if ($deletionRequest->status !== DELETION_REQUEST_PENDING) {
                throw new Exception(__('This request has already been reviewed.'));
            }

            $user = User::findOrFail($deletionRequest->user_id);

            // Restore user to active
            $user->status = USER_STATUS_ACTIVE;
            $user->save();

            // Mark the request as rejected
            $deletionRequest->status      = DELETION_REQUEST_REJECTED;
            $deletionRequest->reviewed_by = Auth::id();
            $deletionRequest->reviewed_at = now();
            $deletionRequest->admin_note  = $request->admin_note;
            $deletionRequest->save();

            DB::commit();
            return $this->success([], __('Deletion request rejected. The account has been restored.'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }
}
