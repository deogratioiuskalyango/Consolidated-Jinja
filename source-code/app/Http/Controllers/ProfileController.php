<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Models\FileManager;
use App\Models\Owner;
use App\Models\Tenant;
use App\Models\TenantDetails;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function myProfile()
    {
        $data['pageTitle'] = __('My Profile');

        if (in_array(auth()->user()->role, [USER_ROLE_OWNER, USER_ROLE_TEAM_MEMBER])) {
            $data['owner'] = Owner::query()
                ->leftJoin('file_managers', 'owners.logo_id', '=', 'file_managers.id')
                ->where('owners.user_id', auth()->id())
                ->select(['owners.*', 'file_managers.folder_name', 'file_managers.file_name'])
                ->first();
        }

        if (auth()->user()->role == USER_ROLE_TENANT) {
            $data['tenant'] = Tenant::where('user_id', auth()->id())->first();
            if ($data['tenant']) {
                $data['details'] = TenantDetails::firstOrCreate(
                    ['tenant_id' => $data['tenant']->id],
                    ['tenant_id' => $data['tenant']->id]
                );
            } else {
                $data['details'] = null;
            }
        }

        // Pass any pending/rejected deletion request so the view can show status
        if (in_array(auth()->user()->role, [USER_ROLE_TENANT, USER_ROLE_MAINTAINER])) {
            $data['deletionRequest'] = AccountDeletionRequest::where('user_id', auth()->id())
                ->whereIn('status', [DELETION_REQUEST_PENDING, DELETION_REQUEST_REJECTED])
                ->latest()
                ->first();
        } else {
            $data['deletionRequest'] = null;
        }

        return view('common.profile.my-profile', $data);
    }

    public function profileUpdate(Request $request)
    {
        $authId = auth()->id();
        $request->validate([
            'first_name'      => 'required|max:191',
            'last_name'       => 'required|max:191',
            'email'           => 'required|email|max:191|unique:users,email,' . $authId,
            'contact_number'  => 'nullable|numeric|unique:users,contact_number,' . $authId,
            'whatsapp_number' => 'nullable|string|max:20',
            'image'           => 'nullable|file|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($authId);
            $user->first_name      = $request->first_name;
            $user->last_name       = $request->last_name;
            $user->contact_number  = $request->contact_number;
            $user->whatsapp_number = $request->whatsapp_number;
            $user->date_of_birth   = $request->date_of_birth;
            $user->nid_number      = $request->nid_number;

            if (in_array(auth()->user()->role, [USER_ROLE_ADMIN, USER_ROLE_OWNER, USER_ROLE_TEAM_MEMBER])) {
                $user->email = $request->email;
            }
            $user->save();

            // Owner: save print details + logo
            if (auth()->user()->role == USER_ROLE_OWNER) {
                $owner = Owner::firstOrCreate(
                    ['user_id' => auth()->id()],
                    ['user_id' => auth()->id()]
                );
                $owner->print_name    = $request->print_name;
                $owner->print_address = $request->print_address;
                $owner->print_contact = $request->print_contact;
                $owner->save();

                if ($request->hasFile('print_logo')) {
                    $existFile = FileManager::where('origin_type', 'App\Models\Owner')
                        ->where('id', $owner->logo_id)
                        ->first();

                    if ($existFile) {
                        $existFile->removeFile();
                        $upload = $existFile->updateUpload($existFile->id, 'Owner', $request->print_logo);
                    } else {
                        $newFile = new FileManager();
                        $upload  = $newFile->upload('Owner', $request->print_logo);
                    }

                    if ($upload['status']) {
                        $owner->logo_id = $upload['file']->id;
                        $owner->save();
                        $upload['file']->origin_id   = $owner->id;
                        $upload['file']->origin_type = 'App\Models\Owner';
                        $upload['file']->save();
                    } else {
                        throw new Exception($upload['message']);
                    }
                }
            }

            // Tenant: save additional info + address details
            if (auth()->user()->role == USER_ROLE_TENANT) {
                $tenant = Tenant::where('user_id', auth()->id())->first();
                if ($tenant) {
                    $tenant->job           = $request->job;
                    $tenant->family_member = $request->family_member;
                    $tenant->age           = $request->age;
                    $tenant->save();

                    $details = TenantDetails::firstOrCreate(
                        ['tenant_id' => $tenant->id],
                        ['tenant_id' => $tenant->id]
                    );
                    $details->permanent_country_id = $request->permanent_country_id;
                    $details->permanent_state_id   = $request->permanent_state_id;
                    $details->permanent_city_id    = $request->permanent_city_id;
                    $details->permanent_address    = $request->permanent_address;
                    $details->permanent_zip_code   = $request->permanent_zip_code;
                    $details->previous_country_id  = $request->previous_country_id;
                    $details->previous_state_id    = $request->previous_state_id;
                    $details->previous_city_id     = $request->previous_city_id;
                    $details->previous_address     = $request->previous_address;
                    $details->previous_zip_code    = $request->previous_zip_code;
                    $details->save();
                }
            }

            // Profile photo upload (all roles)
            if ($request->hasFile('image')) {
                $existFile = FileManager::where('origin_type', 'App\Models\User')
                    ->where('origin_id', $user->id)
                    ->first();

                if ($existFile) {
                    $existFile->removeFile();
                    $upload = $existFile->updateUpload($existFile->id, 'User', $request->image);
                } else {
                    $newFile = new FileManager();
                    $upload  = $newFile->upload('User', $request->image);
                }

                if ($upload['status']) {
                    $upload['file']->origin_id   = $user->id;
                    $upload['file']->origin_type = 'App\Models\User';
                    $upload['file']->save();
                } else {
                    throw new Exception($upload['message']);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', __('Profile updated successfully.'));
    }

    public function changePassword()
    {
        $data['pageTitle'] = __('Change Password');
        return view('common.profile.change-password', $data);
    }

    public function changePasswordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:6',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return redirect()->back()->with('error', __('Current password does not match.'));
        }

        $user = User::findOrFail(Auth::id());
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', __('Password changed successfully.'));
    }

    public function deleteMyAccount(Request $request)
    {
        $request->validate([
            'email'    => 'email|required',
            'password' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $authUser = Auth::user();

            if (!Hash::check($request->password, $authUser->password) || $authUser->email !== $request->email) {
                throw new Exception(__('Information does not match.'));
            }
            if (in_array($authUser->role, [USER_ROLE_ADMIN, USER_ROLE_OWNER])) {
                throw new Exception(__('This account cannot be deleted here.'));
            }

            // Block duplicate pending requests
            $existing = AccountDeletionRequest::where('user_id', $authUser->id)
                ->where('status', DELETION_REQUEST_PENDING)
                ->first();
            if ($existing) {
                throw new Exception(__('You already have a pending deletion request. Please wait for admin review.'));
            }

            // Create the deletion request
            AccountDeletionRequest::create([
                'user_id' => $authUser->id,
                'status'  => DELETION_REQUEST_PENDING,
                'reason'  => $request->reason ?? null,
            ]);

            // Mark the user as deletion-requested (they can still log in but see a banner)
            $user = User::findOrFail($authUser->id);
            $user->status = USER_STATUS_DELETION_REQUESTED;
            $user->save();

            DB::commit();
            auth()->logout();

            return $this->success([], __('Your account deletion request has been submitted. An admin will review it and your account will be removed shortly.'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error([], getErrorMessage($e, $e->getMessage()));
        }
    }
}
