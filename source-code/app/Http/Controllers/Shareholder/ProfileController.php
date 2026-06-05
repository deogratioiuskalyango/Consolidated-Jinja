<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Shareholder;
use App\Models\GovernanceAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $data['pageTitle']   = __('My Profile');
        $data['shareholder'] = auth()->user()->shareholder->load('shareClass', 'user', 'loginDevices');
        $data['user']        = auth()->user();
        return view('shareholder.profile', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|max:191',
            'last_name'      => 'required|max:191',
            'contact_number' => 'nullable|numeric',
            'address'        => 'nullable|max:500',
            'nid_number'     => 'nullable|max:100',
            'image'          => 'nullable|file|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $user->update($request->only('first_name', 'last_name', 'contact_number', 'date_of_birth'));

            $shareholder = $user->shareholder;
            $shareholder->update($request->only('address', 'nid_number', 'passport_number'));

            // Profile photo
            if ($request->hasFile('image')) {
                $existFile = \App\Models\FileManager::where('origin_type', 'App\Models\User')
                    ->where('origin_id', $user->id)->first();
                if ($existFile) {
                    $existFile->removeFile();
                    $upload = $existFile->updateUpload($existFile->id, 'User', $request->image);
                } else {
                    $newFile = new \App\Models\FileManager();
                    $upload = $newFile->upload('User', $request->image);
                }
                if ($upload['status']) {
                    $upload['file']->origin_id   = $user->id;
                    $upload['file']->origin_type = 'App\Models\User';
                    $upload['file']->save();
                }
            }

            GovernanceAuditLog::record(AUDIT_PROFILE_UPDATE, $shareholder, [], 'Shareholder updated their profile');
            DB::commit();
            return redirect()->back()->with('success', __('Profile updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function changePassword()
    {
        $data['pageTitle'] = __('Change Password');
        return view('shareholder.change-password', $data);
    }

    public function changePasswordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:6',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return redirect()->back()->with('error', __('Current password does not match.'));
        }

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear force_password_change flag
        $shareholder = $user->shareholder;
        if ($shareholder) {
            $shareholder->force_password_change = false;
            $shareholder->save();
        }

        GovernanceAuditLog::record(AUDIT_PASSWORD_CHANGE, $shareholder, [], 'Shareholder changed their password');
        return redirect()->route('shareholder.dashboard')->with('success', __('Password changed successfully.'));
    }
}
