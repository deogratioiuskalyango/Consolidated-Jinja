<?php

namespace Botble\EWallet\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\Customer;
use Botble\EWallet\Helpers\WalletHelper;
use Botble\EWallet\Models\Wallet;
use Botble\EWallet\Models\WalletTransaction;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Auth;

class WalletController extends BaseController
{
    public function __construct(protected WalletHelper $walletHelper)
    {
        $version = EcommerceHelper::getAssetVersion();

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);
    }

    public function index()
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        abort_unless($customer, 404);

        SeoHelper::setTitle(trans('plugins/e-wallet::e-wallet.customer.my_wallet'));

        $wallet = Wallet::query()->firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'balance' => 0,
                'currency_code' => $this->walletHelper->getDefaultCurrency(),
            ]
        );

        $transactions = WalletTransaction::query()
            ->with('reference')
            ->where('wallet_id', $wallet->id)
            ->latest('created_at')
            ->paginate(20);

        Theme::breadcrumb()
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.home'), route('public.index'))
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.my_account'), route('customer.overview'))
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.my_wallet'));

        return Theme::scope(
            'e-wallet.wallet',
            compact('wallet', 'transactions', 'customer'),
            'plugins/e-wallet::themes.wallet'
        )->render();
    }
}
