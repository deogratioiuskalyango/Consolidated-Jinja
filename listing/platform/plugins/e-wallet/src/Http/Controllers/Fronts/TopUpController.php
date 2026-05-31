<?php

namespace Botble\EWallet\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\EWallet\Enums\TopUpStatusEnum;
use Botble\EWallet\Helpers\WalletHelper;
use Botble\EWallet\Http\Requests\TopUpRequest;
use Botble\EWallet\Models\WalletTopUp;
use Botble\EWallet\Services\TopUpService;
use Botble\EWallet\Services\WalletService;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Payment\Models\Payment;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class TopUpController extends BaseController
{
    public function __construct(
        protected TopUpService $topUpService,
        protected WalletService $walletService,
        protected WalletHelper $helper
    ) {
        $version = EcommerceHelper::getAssetVersion();

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);
    }

    public function create()
    {
        abort_unless($this->helper->isTopUpEnabled(), 404);

        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return redirect()->route('customer.login');
        }

        SeoHelper::setTitle(trans('plugins/e-wallet::e-wallet.topup.title'));

        Theme::breadcrumb()
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.home'), route('public.index'))
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.my_account'), route('customer.overview'))
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.my_wallet'), route('customer.e-wallet.index'))
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.topup'));

        $wallet = $this->walletService->getOrCreateWallet($customer->id);
        $minAmount = $this->helper->getMinTopUp() / 100;
        $maxAmount = $this->helper->getMaxTopUp() / 100;
        $predefinedAmounts = $this->generatePredefinedAmounts($minAmount, $maxAmount);

        return Theme::scope(
            'e-wallet.topup.form',
            compact('wallet', 'minAmount', 'maxAmount', 'predefinedAmounts'),
            'plugins/e-wallet::themes.topup.form'
        )->render();
    }

    public function store(TopUpRequest $request)
    {
        $customer = Auth::guard('customer')->user();
        $amountCents = (int) ($request->input('amount') * 100);

        try {
            $topup = $this->topUpService->createTopUp($customer->id, $amountCents);
            session(['wallet_topup_id' => $topup->id]);

            return redirect()->route('customer.e-wallet.topup.checkout', $topup->code);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }
    }

    public function checkout(string $code)
    {
        $customer = Auth::guard('customer')->user();
        $topup = WalletTopUp::query()
            ->where('code', $code)
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'processing'])
            ->firstOrFail();

        if ($topup->status->getValue() === TopUpStatusEnum::PROCESSING) {
            $topup->update(['status' => TopUpStatusEnum::PENDING]);
        }

        session(['wallet_topup_id' => $topup->id]);

        SeoHelper::setTitle(trans('plugins/e-wallet::e-wallet.topup.checkout'));

        Theme::breadcrumb()
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.home'), route('public.index'))
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.my_wallet'), route('customer.e-wallet.index'))
            ->add(trans('plugins/e-wallet::e-wallet.topup.checkout'));

        $selectedMethod = $topup->payment_method ?: PaymentMethods::getSelectingMethod();

        $paymentData = [
            'amount' => $topup->amount / 100,
            'currency' => cms_currency()->getApplicationCurrency()->title,
            'name' => $customer->name,
            'selected' => $selectedMethod,
            'default' => PaymentMethods::getDefaultMethod(),
            'selecting' => $selectedMethod,
        ];

        $additionalMethodsHtml = apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, $paymentData);
        $defaultMethodsHtml = PaymentMethods::render();

        $paymentMethodsHtml = '<ul class="list-group list_payment_method">' . $additionalMethodsHtml . $defaultMethodsHtml . '</ul>';

        $selectedPaymentMethod = $topup->payment_method;

        return Theme::scope(
            'e-wallet.topup.checkout',
            compact('topup', 'paymentMethodsHtml', 'selectedPaymentMethod'),
            'plugins/e-wallet::themes.topup.checkout'
        )->render();
    }

    public function processPayment(Request $request, string $code)
    {
        $customer = Auth::guard('customer')->user();
        $topup = WalletTopUp::query()
            ->where('code', $code)
            ->where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->firstOrFail();

        session(['wallet_topup_id' => $topup->id]);
        session(['wallet_topup_processing' => true]);

        $paymentMethod = $request->input('payment_method');

        $request->merge([
            'amount' => $topup->amount / 100,
            'currency' => cms_currency()->getApplicationCurrency()->title,
            'name' => $customer->name,
            'email' => $customer->email,
            'wallet_topup_id' => $topup->id,
            'wallet_topup_code' => $topup->code,
        ]);

        $paymentData = [
            'error' => false,
            'message' => null,
            'amount' => (float) format_price($topup->amount / 100, null, true),
            'currency' => strtoupper(cms_currency()->getApplicationCurrency()->title),
            'type' => $paymentMethod,
            'charge_id' => null,
            'order_id' => $topup->code, // Use topup code as merchant reference for payment gateways
            'description' => trans('plugins/e-wallet::e-wallet.topup.payment_description', [
                'code' => $topup->code,
            ]),
            'customer_id' => $customer->id,
            'customer_type' => $customer::class,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone ?? '',
            'customer_first_name' => $customer->name ? explode(' ', $customer->name, 2)[0] : 'Customer',
            'customer_last_name' => $customer->name && strpos($customer->name, ' ') !== false ? explode(' ', $customer->name, 2)[1] : '',
            'return_url' => route('customer.e-wallet.topup.callback', $topup->code),
            'callback_url' => route('customer.e-wallet.topup.callback', $topup->code),
        ];

        $paymentData = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $paymentData, $request);

        if (! empty($paymentData['checkoutUrl'])) {
            $topup->update([
                'payment_method' => $paymentMethod,
                'status' => 'processing',
            ]);

            return redirect($paymentData['checkoutUrl']);
        }

        if (! empty($paymentData['error']) && $paymentData['error'] === true) {
            $topup->update(['payment_method' => $paymentMethod]);

            return back()->withErrors([
                'payment' => $paymentData['message'] ?? trans('plugins/e-wallet::e-wallet.errors.payment_failed'),
            ]);
        }

        if (! empty($paymentData['charge_id'])) {
            $this->topUpService->completeTopUp($topup, $paymentData['charge_id'], $paymentMethod);

            return redirect()->route('customer.e-wallet.topup.success', $topup->code);
        }

        $topup->update([
            'payment_method' => $paymentMethod,
            'status' => TopUpStatusEnum::PROCESSING,
        ]);

        return redirect()->route('customer.e-wallet.topup.success', $topup->code)
            ->with('pending', true);
    }

    public function callback(Request $request, string $code)
    {
        $customer = Auth::guard('customer')->user();
        $topup = WalletTopUp::query()
            ->where('code', $code)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        session()->forget('wallet_topup_id');

        if ($topup->isCompleted()) {
            return redirect()->route('customer.e-wallet.topup.success', $topup->code);
        }

        if ($topup->status->getValue() === TopUpStatusEnum::PROCESSING) {
            return redirect()->route('customer.e-wallet.topup.success', $topup->code)
                ->with('pending', true);
        }

        return redirect()->route('customer.e-wallet.topup.checkout', $topup->code)
            ->withErrors(['payment' => trans('plugins/e-wallet::e-wallet.errors.payment_failed')]);
    }

    protected function generatePredefinedAmounts(float $minAmount, float $maxAmount): array
    {
        $amounts = [];
        $baseAmounts = [10, 25, 50, 100, 250, 500, 1000, 2500, 5000, 10000, 25000, 50000, 100000];

        foreach ($baseAmounts as $amount) {
            if ($amount >= $minAmount && $amount <= $maxAmount && count($amounts) < 6) {
                $amounts[] = $amount;
            }
        }

        if (empty($amounts)) {
            $amounts[] = (int) $minAmount;
            $step = ($maxAmount - $minAmount) / 5;

            for ($i = 1; $i < 5; $i++) {
                $amount = (int) ($minAmount + $step * $i);
                if ($amount <= $maxAmount) {
                    $amounts[] = $amount;
                }
            }

            if ((int) $maxAmount != (int) $minAmount) {
                $amounts[] = (int) $maxAmount;
            }

            $amounts = array_unique($amounts);
        }

        return array_values($amounts);
    }

    public function success(string $code, Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $topup = WalletTopUp::query()
            ->where('code', $code)
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['completed', 'processing', 'pending'])
            ->firstOrFail();

        if ($topup->isPending() && $request->filled('charge_id')) {
            $chargeId = $request->input('charge_id');
            $paymentMethod = null;

            if (class_exists(Payment::class)) {
                $payment = Payment::query()
                    ->where('charge_id', $chargeId)
                    ->first();

                $paymentMethod = $payment?->payment_channel;
            }

            $this->topUpService->completeTopUp($topup, $chargeId, $paymentMethod);
            $topup->refresh();
        }

        SeoHelper::setTitle(trans('plugins/e-wallet::e-wallet.topup.success'));

        Theme::breadcrumb()
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.home'), route('public.index'))
            ->add(trans('plugins/e-wallet::e-wallet.breadcrumbs.my_wallet'), route('customer.e-wallet.index'))
            ->add(trans('plugins/e-wallet::e-wallet.topup.success'));

        $wallet = $this->walletService->getOrCreateWallet($customer->id);
        $isPending = session('pending', false) || $topup->status->getValue() === TopUpStatusEnum::PROCESSING;

        return Theme::scope(
            'e-wallet.topup.success',
            compact('topup', 'wallet', 'isPending'),
            'plugins/e-wallet::themes.topup.success'
        )->render();
    }
}
