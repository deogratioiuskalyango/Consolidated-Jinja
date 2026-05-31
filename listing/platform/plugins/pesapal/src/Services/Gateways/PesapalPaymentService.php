<?php

namespace Botble\Pesapal\Services\Gateways;

use Botble\Pesapal\Services\Abstracts\PesapalPaymentAbstract;
use Botble\Pesapal\Support\PesapalHelper;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PesapalPaymentService extends PesapalPaymentAbstract
{
    public function makePayment(array $data): ?string
    {
        try {
            $this->amount = $data['amount'];
            $this->currency = strtoupper($data['currency'] ?? 'KES');

            $consumerKey = get_payment_setting('consumer_key', PESAPAL_PAYMENT_METHOD_NAME);
            $consumerSecret = get_payment_setting('consumer_secret', PESAPAL_PAYMENT_METHOD_NAME);
            $mode = get_payment_setting('mode', PESAPAL_PAYMENT_METHOD_NAME, 'sandbox');

            if (empty($consumerKey) || empty($consumerSecret)) {
                $this->setErrorMessage(trans('plugins/pesapal::pesapal.missing_credentials'));

                return null;
            }

            $helper = new PesapalHelper($consumerKey, $consumerSecret, $mode);

            // Extract customer data from order
            $customerEmail = Arr::get($data, 'customer_email');
            $customerPhone = Arr::get($data, 'customer_phone');
            $customerFirstName = Arr::get($data, 'customer_first_name');
            $customerLastName = Arr::get($data, 'customer_last_name');

            // If customer data not in data array, try to get from order
            if (empty($customerEmail) && !empty($data['order_id'])) {
                $orderIds = is_array($data['order_id']) ? $data['order_id'] : [$data['order_id']];
                if (!empty($orderIds)) {
                    $order = \Botble\Ecommerce\Models\Order::find($orderIds[0]);
                    if ($order) {
                        $customerEmail = $order->user->email ?? $order->address->email ?? '';
                        $customerPhone = $order->address->phone ?? '';
                        $customerFirstName = $order->address->name ?? '';
                        $customerLastName = '';
                    }
                }
            }

            // Get order ID for reference
            $orderIds = is_array($data['order_id']) ? $data['order_id'] : [$data['order_id']];
            $orderId = !empty($orderIds) ? $orderIds[0] : Arr::get($data, 'charge_id', uniqid('ORDER-'));

            // Prepare order data
            $orderData = [
                'amount' => number_format($this->amount, 2, '.', ''),
                'currency' => $this->currency,
                'description' => Arr::get($data, 'description', 'Order Payment'),
                'type' => 'MERCHANT',
                'reference' => (string) $orderId, // Use order ID as reference
                'first_name' => $customerFirstName ?: 'Customer',
                'last_name' => $customerLastName ?: '',
                'email' => $customerEmail ?: '',
                'phone_number' => $customerPhone ?: '',
            ];

            // Get callback URL
            $callbackUrl = route('pesapal.payment.callback');

            // Generate iframe URL
            $iframeUrl = $helper->generateIframeUrl($orderData, $callbackUrl);

            if ($iframeUrl) {
                return $iframeUrl;
            }

            $this->setErrorMessage(trans('plugins/pesapal::pesapal.failed_to_generate_payment_url'));

            return null;
        } catch (Exception $exception) {
            Log::error('PesaPal Payment Error: ' . $exception->getMessage());
            $this->setErrorMessage($exception->getMessage());

            return null;
        }
    }

    public function afterMakePayment(string $chargeId, array $data): string
    {
        // This is called after payment is made
        // For PesaPal, we handle status updates via IPN
        return $chargeId;
    }

    public function supportedCurrencyCodes(): array
    {
        return [
            'KES', // Kenyan Shilling
            'UGX', // Ugandan Shilling
            'TZS', // Tanzanian Shilling
            'USD', // US Dollar
        ];
    }
}

