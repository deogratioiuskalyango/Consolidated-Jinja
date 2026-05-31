<?php

namespace Botble\Hotel\Http\Controllers\Front\Customers;

use Botble\ACL\Traits\ResetsPasswords;
use Botble\Base\Http\Controllers\BaseController;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends BaseController
{
    use ResetsPasswords;

    public string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('customer.guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        SeoHelper::setTitle(__('Reset Password'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Reset Password'), route('customer.password.reset'));

        return Theme::scope(
            'hotel.customers.passwords.reset',
            [
                'token' => $token,
                'email' => $request->email,
            ],
            'plugins/hotel::themes.customers.passwords.reset'
        )
            ->render();
    }

    public function broker(): PasswordBroker
    {
        return Password::broker('customers');
    }

    protected function guard(): StatefulGuard
    {
        return auth('customer');
    }
}
