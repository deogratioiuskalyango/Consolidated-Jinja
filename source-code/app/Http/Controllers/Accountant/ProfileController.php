<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $user       = auth()->user();
        $accountant = $user->accountant->load('user');

        return view('accountant.profile', [
            'user'       => $user,
            'accountant' => $accountant,
            'pageTitle'  => 'My Profile',
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:191',
            'last_name'      => 'required|string|max:191',
            'contact_number' => 'nullable|string|max:50',
            'image'          => 'nullable|file|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $user->update($request->only('first_name', 'last_name', 'contact_number'));

            // Profile photo upload — mirrors ShareholderProfileController pattern
            if ($request->hasFile('image')) {
                $existFile = \App\Models\FileManager::where('origin_type', 'App\Models\User')
                    ->where('origin_id', $user->id)->first();
                if ($existFile) {
                    $existFile->removeFile();
                    $upload = $existFile->updateUpload($existFile->id, 'User', $request->image);
                } else {
                    $newFile = new \App\Models\FileManager();
                    $upload  = $newFile->upload('User', $request->image);
                }
                if (!empty($upload['status'])) {
                    $upload['file']->origin_id   = $user->id;
                    $upload['file']->origin_type = 'App\Models\User';
                    $upload['file']->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Profile updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function changePassword()
    {
        return view('accountant.change-password', [
            'pageTitle' => 'Change Password',
        ]);
    }

    public function changePasswordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:6',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Current password does not match.');
        }

        DB::beginTransaction();
        try {
            $user           = auth()->user();
            $user->password = Hash::make($request->password);
            $user->save();

            // Clear force_password_change flag on accountant record
            $accountant = $user->accountant;
            if ($accountant) {
                $accountant->force_password_change = false;
                $accountant->save();
            }

            DB::commit();
            return redirect()->route('accountant.dashboard')->with('success', 'Password changed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
