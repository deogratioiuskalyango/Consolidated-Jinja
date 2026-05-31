<?php

namespace Botble\EWallet\Providers;

use Botble\Base\Facades\Html;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Order;
use Botble\EWallet\Enums\TransactionTypeEnum;
use Botble\EWallet\Exceptions\InsufficientBalanceException;
use Botble\EWallet\Forms\EWalletPaymentMethodForm;
use Botble\EWallet\Helpers\WalletHelper;
use Botble\EWallet\Models\WalletTopUp;
use Botble\EWallet\Services\TopUpService;
use Botble\EWallet\Services\WalletPaymentService;
use Botble\EWallet\Services\WalletService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Facades\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPaymentMethodEnum();
        $this->registerPaymentMethod();
        $this->registerPaymentMethodSettings();
        $this->registerCheckoutHooks();
        $this->registerPaymentProcessingHook();
        $this->registerOrderDetailHooks();
        $this->registerRefundHooks();
        $this->registerFrontendAssets();
        $this->registerTopUpPaymentData();
        $this->registerTopUpRedirectUrl();
        $this->registerTopUpPaymentCompletion();
        $this->registerTopUpPaymentMethodFilter();
    }

    protected function registerPaymentMethodEnum(): void
    {
        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class === PaymentMethodEnum::class) {
                $values['WALLET'] = E_WALLET_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 99, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class === PaymentMethodEnum::class && $value === E_WALLET_PAYMENT_METHOD_NAME) {
                return trans('plugins/e-wallet::e-wallet.checkout.pay_with_wallet');
            }

            return $value;
        }, 99, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class === PaymentMethodEnum::class && $value === E_WALLET_PAYMENT_METHOD_NAME) {
                return Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )->toHtml();
            }

            return $value;
        }, 99, 2);
    }

    protected function registerPaymentMethod(): void
    {
        if (! defined('PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS')) {
            return;
        }

        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, function ($html, $data) {
            if (get_payment_setting('status', E_WALLET_PAYMENT_METHOD_NAME) != 1) {
                return $html;
            }

            $helper = app(WalletHelper::class);

            if (! $helper->isEnabled()) {
                return $html;
            }

            $currentRoute = request()->route()?->getName();

            if ($currentRoute && str_starts_with($currentRoute, 'customer.e-wallet.topup')) {
                return $html;
            }

            $customer = Auth::guard('customer')->user();
            $walletService = app(WalletService::class);
            $balance = $customer ? $walletService->getBalance($customer->id) : 0;
            $orderAmount = (int) (($data['amount'] ?? 0) * 100);

            PaymentMethods::method(E_WALLET_PAYMENT_METHOD_NAME, [
                'html' => view('plugins/e-wallet::themes.checkout.payment-method', array_merge($data, [
                    'balance' => $balance,
                    'formattedBalance' => format_price($balance / 100),
                    'canPay' => $customer && $balance >= $orderAmount,
                    'orderAmount' => $orderAmount,
                    'isLoggedIn' => (bool) $customer,
                ]))->render(),
                'priority' => 1,
            ]);

            return $html;
        }, 1, 2);
    }

    protected function registerPaymentMethodSettings(): void
    {
        if (! defined('PAYMENT_METHODS_SETTINGS_PAGE')) {
            return;
        }

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, function (?string $settings) {
            return $settings . EWalletPaymentMethodForm::create()->renderForm();
        }, 50);
    }

    protected function registerCheckoutHooks(): void
    {
        add_filter('ecommerce_checkout_form_before_payment_form', function (?string $html) {
            $helper = app(WalletHelper::class);

            if (! $helper->isEnabled()) {
                return $html;
            }

            $customer = Auth::guard('customer')->user();
            if (! $customer) {
                return $html;
            }

            $balance = app(WalletService::class)->getBalance($customer->id);

            if ($balance <= 0) {
                return $html;
            }

            $html .= view('plugins/e-wallet::themes.checkout.partials.wallet-balance', [
                'balance' => $balance,
                'formattedBalance' => format_price($balance / 100),
            ])->render();

            return $html;
        }, 50);
    }

    protected function registerPaymentProcessingHook(): void
    {
        if (! defined('PAYMENT_FILTER_AFTER_POST_CHECKOUT')) {
            return;
        }

        add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, function (array $data, Request $request) {
            if ($data['type'] !== E_WALLET_PAYMENT_METHOD_NAME) {
                return $data;
            }

            $helper = app(WalletHelper::class);
            if (! $helper->isEnabled()) {
                $data['error'] = true;
                $data['message'] = trans('plugins/e-wallet::e-wallet.errors.wallet_disabled');

                return $data;
            }

            $customer = Auth::guard('customer')->user();
            if (! $customer) {
                $data['error'] = true;
                $data['message'] = trans('plugins/e-wallet::e-wallet.errors.customer_required');

                return $data;
            }

            $orderIds = $request->input('order_id');
            $orders = Order::query()->whereIn('id', (array) $orderIds)->get();

            if ($orders->isEmpty() || ! $orders->first()->user_id) {
                $data['error'] = true;
                $data['message'] = trans('plugins/e-wallet::e-wallet.errors.customer_required');

                return $data;
            }

            $amountCents = (int) round(($data['amount'] ?? 0) * 100);
            $paymentService = app(WalletPaymentService::class);

            try {
                $transactionIds = [];

                foreach ($orders as $order) {
                    $orderAmount = (int) round($order->amount * 100);
                    $transaction = $paymentService->processOrderPayment($order, $orderAmount);
                    $transactionIds[] = $transaction->id;
                }

                $chargeId = 'wallet_' . implode('_', $transactionIds);
                $data['charge_id'] = $chargeId;
                $data['error'] = false;
                $data['message'] = trans('plugins/e-wallet::e-wallet.checkout.payment_success');

                do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'charge_id' => $chargeId,
                    'order_id' => $orderIds,
                    'customer_id' => $customer->id,
                    'customer_type' => get_class($customer),
                    'payment_channel' => E_WALLET_PAYMENT_METHOD_NAME,
                    'status' => PaymentStatusEnum::COMPLETED,
                ]);

            } catch (InsufficientBalanceException $e) {
                $data['error'] = true;
                $data['message'] = $e->getMessage();
            } catch (\Exception $e) {
                $data['error'] = true;
                $data['message'] = trans('plugins/e-wallet::e-wallet.errors.payment_failed');
                report($e);
            }

            return $data;
        }, 999, 2);
    }

    protected function registerOrderDetailHooks(): void
    {
        add_filter('ecommerce_order_detail_sidebar_bottom', function (?string $html, $order) {
            $helper = app(WalletHelper::class);

            if (! $helper->isEnabled()) {
                return $html;
            }

            $walletService = app(WalletService::class);
            $transactions = $walletService->getTransactionsByOrder($order);

            if ($transactions->isEmpty()) {
                return $html;
            }

            $html .= view('plugins/e-wallet::admin.orders.wallet-info', [
                'order' => $order,
                'transactions' => $transactions,
            ])->render();

            return $html;
        }, 99, 2);
    }

    protected function registerRefundHooks(): void
    {
        add_filter('ecommerce_order_refund_form_after', function (?string $html, $order) {
            $helper = app(WalletHelper::class);

            if (! $helper->isEnabled()) {
                return $html;
            }

            if (! $order->user_id) {
                return $html;
            }

            $html .= view('plugins/e-wallet::admin.refund.wallet-info', [
                'order' => $order,
            ])->render();

            return $html;
        }, 10, 2);

        if (! defined('ACTION_AFTER_POST_ORDER_REFUNDED_ECOMMERCE')) {
            return;
        }

        add_filter(ACTION_AFTER_POST_ORDER_REFUNDED_ECOMMERCE, function (BaseHttpResponse $response, Order $order, Request $request) {
            $helper = app(WalletHelper::class);

            if (! $helper->isEnabled()) {
                return $response;
            }

            if (get_wallet_setting('refund_to_wallet', 'wallet') !== 'wallet') {
                return $response;
            }

            if (! $order->user_id) {
                return $response;
            }

            $refundAmount = (float) $request->input('refund_amount', 0);

            if ($refundAmount <= 0) {
                return $response;
            }

            $amountCents = (int) round($refundAmount * 100);
            $idempotencyKey = 'order_refund_' . $order->id . '_' . now()->timestamp;

            $walletService = app(WalletService::class);

            $walletService->credit(
                customerId: $order->user_id,
                amountCents: $amountCents,
                type: TransactionTypeEnum::REFUND,
                referenceType: Order::class,
                referenceId: $order->id,
                description: trans('plugins/e-wallet::e-wallet.transaction.order_refund', [
                    'code' => $order->code,
                ]),
                idempotencyKey: $idempotencyKey,
                metadata: [
                    'order_code' => $order->code,
                    'order_id' => $order->id,
                    'refund_amount' => $refundAmount,
                    'refund_note' => $request->input('refund_note'),
                ]
            );

            return $response;
        }, 10, 3);
    }

    protected function registerFrontendAssets(): void
    {
        if (! defined('THEME_FRONT_HEADER')) {
            return;
        }

        add_filter(THEME_FRONT_HEADER, function (?string $html) {
            if (! request()->is('customer/e-wallet*')) {
                return $html;
            }

            $walletCss = asset('vendor/core/plugins/e-wallet/css/wallet.css');

            $html .= sprintf('<link rel="stylesheet" href="%s">', $walletCss);

            return $html;
        }, 99);
    }

    protected function registerTopUpPaymentData(): void
    {
        if (! defined('PAYMENT_FILTER_PAYMENT_DATA')) {
            return;
        }

        add_filter(PAYMENT_FILTER_PAYMENT_DATA, function (array $data, Request $request) {
            $topupId = $request->input('wallet_topup_id') ?: session('wallet_topup_id');

            if (! $topupId && ! session('wallet_topup_processing')) {
                return $data;
            }

            if (! $topupId) {
                return $data;
            }

            $topup = WalletTopUp::query()->find($topupId);

            if (! $topup) {
                return $data;
            }

            $customer = Auth::guard('customer')->user();

            if (! $customer || $topup->customer_id !== $customer->id) {
                return $data;
            }

            session()->forget('wallet_topup_processing');

            $amount = (float) format_price($topup->amount / 100, null, true);

            $customerNameParts = $customer->name ? explode(' ', $customer->name, 2) : ['Customer', ''];
            
            return [
                'amount' => $amount,
                'currency' => get_application_currency()->title,
                'description' => trans('plugins/e-wallet::e-wallet.topup.payment_description', [
                    'code' => $topup->code,
                ]),
                'order_id' => $topup->code, // Use topup code as merchant reference
                'customer_id' => $customer->id,
                'customer_type' => get_class($customer),
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone ?? '',
                'customer_first_name' => $customerNameParts[0],
                'customer_last_name' => $customerNameParts[1] ?? '',
                'return_url' => route('customer.e-wallet.topup.callback', $topup->code),
                'callback_url' => route('customer.e-wallet.topup.callback', $topup->code),
                'checkout_token' => 'topup_' . $topup->code,
                'address' => [
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone ?? '',
                    'country' => '',
                    'state' => '',
                    'city' => '',
                    'address' => '',
                    'zip_code' => '',
                ],
                'products' => [
                    [
                        'id' => 'topup_' . $topup->id,
                        'name' => trans('plugins/e-wallet::e-wallet.topup.wallet_credit'),
                        'image' => null,
                        'price' => $amount,
                        'price_per_order' => $amount,
                        'qty' => 1,
                    ],
                ],
                'orders' => collect([]),
                'is_wallet_topup' => true,
            ];
        }, 1, 2);
    }

    protected function registerTopUpRedirectUrl(): void
    {
        if (! defined('PAYMENT_FILTER_REDIRECT_URL')) {
            return;
        }

        add_filter(PAYMENT_FILTER_REDIRECT_URL, function ($checkoutToken, $default) {
            $topup = $this->resolveTopUpFromTokenOrSession($checkoutToken);

            if (! $topup) {
                return $checkoutToken;
            }

            session()->forget('wallet_topup_id');

            return route('customer.e-wallet.topup.success', $topup->code);
        }, 999, 2);

        add_filter(PAYMENT_FILTER_CANCEL_URL, function ($checkoutToken, $default) {
            $topup = $this->resolveTopUpFromTokenOrSession($checkoutToken);

            if (! $topup) {
                return $checkoutToken;
            }

            return route('customer.e-wallet.topup.checkout', $topup->code);
        }, 999, 2);
    }

    protected function resolveTopUpFromTokenOrSession(?string $checkoutToken): ?WalletTopUp
    {
        if ($checkoutToken) {
            $decodedToken = urldecode($checkoutToken);

            if (preg_match('/topup_(TU-[A-Z0-9]+)/i', $decodedToken, $matches)) {
                $topupCode = strtoupper($matches[1]);

                $topup = WalletTopUp::query()->where('code', $topupCode)->first();

                if ($topup) {
                    return $topup;
                }
            }

            if (preg_match('/\/(TU-[A-Z0-9]+)\/?/i', $decodedToken, $matches)) {
                $topupCode = strtoupper($matches[1]);

                $topup = WalletTopUp::query()->where('code', $topupCode)->first();

                if ($topup) {
                    return $topup;
                }
            }
        }

        $topupId = session('wallet_topup_id');

        if (! $topupId) {
            return null;
        }

        return WalletTopUp::query()->find($topupId);
    }

    protected function registerTopUpPaymentCompletion(): void
    {
        if (! defined('PAYMENT_ACTION_PAYMENT_PROCESSED')) {
            return;
        }

        add_action(PAYMENT_ACTION_PAYMENT_PROCESSED, function (array $data): void {
            $status = $data['status'] ?? null;
            if ($status !== PaymentStatusEnum::COMPLETED) {
                return;
            }

            $topup = $this->resolveTopUpFromPaymentData($data);

            if (! $topup) {
                return;
            }

            if (! in_array($topup->status, ['pending', 'processing'])) {
                return;
            }

            $chargeId = $data['charge_id'] ?? null;
            $paymentChannel = $data['payment_channel'] ?? null;

            $topUpService = app(TopUpService::class);
            $topUpService->completeTopUp($topup, $chargeId, $paymentChannel);
        }, 1);
    }

    protected function resolveTopUpFromPaymentData(array $data): ?WalletTopUp
    {
        $topupId = session('wallet_topup_id');
        if ($topupId) {
            $topup = WalletTopUp::query()->find($topupId);
            if ($topup) {
                return $topup;
            }
        }

        $orderId = $data['order_id'] ?? null;
        if (! empty($orderId)) {
            return null;
        }

        $customerId = $data['customer_id'] ?? null;
        if (! $customerId) {
            $customer = Auth::guard('customer')->user();
            $customerId = $customer?->id;
        }

        if (! $customerId) {
            return null;
        }

        return WalletTopUp::query()
            ->where('customer_id', $customerId)
            ->whereIn('status', ['pending', 'processing'])->latest()
            ->first();
    }

    protected function registerTopUpPaymentMethodFilter(): void
    {
        add_filter('payment_methods_excluded', function (array $excludedMethods) {
            // Only apply filter on top-up pages
            $currentRoute = request()->route()?->getName();
            if (! $currentRoute || ! str_starts_with($currentRoute, 'customer.e-wallet.topup')) {
                return $excludedMethods;
            }

            $allowedMethods = get_allowed_topup_payment_methods();

            foreach (PaymentMethodEnum::toArray() as $method) {
                if (! in_array($method, $allowedMethods) && ! in_array($method, $excludedMethods)) {
                    $excludedMethods[] = $method;
                }
            }

            return $excludedMethods;
        }, 99);
    }
}
