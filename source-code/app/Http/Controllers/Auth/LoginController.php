<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\SystemUserRole;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override the default logout redirect so it always goes to the
     * named login route (which respects APP_URL / HTTPS) instead of
     * the bare redirect('/') in the trait, which can land on HTTP.
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->route('login');
    }

    public function login(LoginRequest $request)
    {
        $field = 'email';
        if (filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (is_numeric($request->input('email'))) {
            $field = 'contact_number';
        }

        $request->merge([$field => $request->input('email')]);

        $credentials = $request->only($field, 'password');

        $remember = request('remember');
        if (!Auth::attempt($credentials, $remember)) {
            return redirect("login")->with('error',  __('Email or password is incorrect'));
        }

        $user = User::where('email', $request->email)->first();
        if (isset($user) && ($user->status == USER_STATUS_UNVERIFIED && $user->role != USER_ROLE_ADMIN)) {
            if (getOption('email_verification_status', 0) == 1) {
                if (is_null($user->verify_token)) {
                    $user->verify_token = str_replace('-', '', Str::uuid()->toString());
                    $user->save();
                }
                $token = $user->verify_token;
                Auth::logout();
                return redirect()->route('user.email.verify', $token)->with('error', __('Verify Your Account'));
            } else {
                $user->status = USER_STATUS_ACTIVE;
                $user->email_verified_at = Carbon::now()->format("Y-m-d H:i:s");
                $user->save();
            }
        } elseif (isset($user) && ($user->status == USER_STATUS_INACTIVE)) {
            Auth::logout();
            return redirect("login")->with('error', __('Your account is inactive. Please contact with admin'));
        } elseif (isset($user) && ($user->status == USER_STATUS_DELETED)) {
            Auth::logout();
            return redirect("login")->with('error', __('Your account has been deleted.'));
        } elseif (isset($user) && ($user->status == USER_STATUS_ACTIVE)) {
            // ── Multi-role check ───────────────────────────────────────────────
            // Build list of all roles this user has (primary + additional)
            session()->forget('active_role_owner_user_id');

            $primarySlug   = SystemUserRole::primaryRoleSlugMap()[$user->role] ?? null;
            $extraSlugs    = SystemUserRole::where('user_id', $user->id)->where('is_active', true)->pluck('role_slug')->toArray();
            $allSlugs      = array_unique(array_merge($primarySlug ? [$primarySlug] : [], $extraSlugs));

            // If user has more than one role, send to role picker
            if (count($allSlugs) > 1) {
                session(['active_role' => $primarySlug]);
                return redirect()->route('role.select.index');
            }

            // Single-role login — existing behaviour
            if ($user->role == USER_ROLE_OWNER || $user->role == USER_ROLE_TEAM_MEMBER) {
                return redirect()->route('owner.dashboard');
            } elseif ($user->role == USER_ROLE_TENANT) {
                if (!is_null($user->tenant->property_id)) {
                    return redirect()->route('tenant.dashboard');
                }
                Auth::logout();
                return redirect("login")->with('error', __('Your account is inactive. Please contact with admin'));
            } elseif ($user->role == USER_ROLE_MAINTAINER) {
                return redirect()->route('maintainer.dashboard');
            } elseif ($user->role == USER_ROLE_SHAREHOLDER) {
                return redirect()->route('shareholder.dashboard');
            } elseif ($user->role == USER_ROLE_ACCOUNTANT) {
                return redirect()->route('accountant.dashboard');
            } elseif ($user->role == USER_ROLE_ADMIN) {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return redirect("login")->with('error', __(SOMETHING_WENT_WRONG));
            }
        } else {
            Auth::logout();
            return redirect("login")->with('error', __(SOMETHING_WENT_WRONG));
        }
    }
}
