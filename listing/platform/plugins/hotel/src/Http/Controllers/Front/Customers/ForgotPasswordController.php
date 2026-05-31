<?php

namespace Botble\Hotel\Http\Controllers\Front\Customers;

use Botble\ACL\Traits\SendsPasswordResetEmails;
use Botble\Base\Http\Controllers\BaseController;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends BaseController
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('customer.guest');
    }

    public function showLinkRequestForm()
    {
        SeoHelper::setTitle(__('Forgot Password'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Forgot Password'), route('customer.password.reset'));

        return Theme::scope(
            'hotel.customers.passwords.email',
            [],
            'plugins/hotel::themes.customers.passwords.email'
        )
            ->render();
    }

    public function broker(): PasswordBroker
    {
        return Password::broker('customers');
    }
}
