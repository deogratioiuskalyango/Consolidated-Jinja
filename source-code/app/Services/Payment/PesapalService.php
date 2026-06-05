<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesapalService extends BasePaymentService
{
    private string $consumerKey;
    private string $consumerSecret;
    private string $baseUrl;
    private int $orderId;

    private const SANDBOX_BASE = 'https://cybqa.pesapal.com/pesapalv3';
    private const LIVE_BASE = 'https://pay.pesapal.com/v3';

    public function __construct($method, $object)
    {
        parent::__construct($method, $object);

        $this->consumerKey = (string) $this->gateway->key;
        $this->consumerSecret = (string) $this->gateway->secret;
        $this->baseUrl = ((int) $this->gateway->mode === GATEWAY_MODE_LIVE)
            ? self::LIVE_BASE
            : self::SANDBOX_BASE;

        if (isset($object['id'])) {
            $this->orderId = (int) $object['id'];
        }
    }

    public function makePayment($amount): array
    {
        $this->setAmount($amount);

        $data = [
            'success' => false,
            'redirect_url' => '',
            'payment_id' => '',
            'message' => SOMETHING_WENT_WRONG,
        ];

        try {
            $token = $this->requestToken();
            $ipnId = $this->resolveIpnId($token);
            $user = auth()->user();
            $nameParts = preg_split('/\s+/', trim($user->name), 2);

            $payload = [
                'id' => (string) $this->orderId,
                'currency' => $this->currency,
                'amount' => (float) number_format($this->amount, 2, '.', ''),
                'description' => 'Rent Payment - Order #' . $this->orderId,
                'callback_url' => $this->callbackUrl,
                'notification_id' => $ipnId,
                'billing_address' => [
                    'email_address' => $user->email,
                    'phone_number' => $user->contact_number ?? '',
                    'country_code' => 'UG',
                    'first_name' => $nameParts[0] ?? $user->name,
                    'last_name' => $nameParts[1] ?? '',
                    'line_1' => '',
                    'city' => 'Kampala',
                    'state' => '',
                    'postal_code' => '',
                    'zip_code' => '',
                ],
            ];

            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->withOptions(['verify' => !env('IS_LOCAL', false)])
                ->post($this->baseUrl . '/api/Transactions/SubmitOrderRequest', $payload);

            if (!$response->successful()) {
                throw new \Exception('Pesapal order request failed: ' . $response->body());
            }

            $body = $response->json();
            $redirectUrl = $body['redirect_url'] ?? null;
            $trackingId = $body['order_tracking_id'] ?? null;

            if (!$redirectUrl || !$trackingId) {
                throw new \Exception('Pesapal did not return a checkout URL.');
            }

            $data['success'] = true;
            $data['redirect_url'] = $redirectUrl;
            $data['payment_id'] = $trackingId;
        } catch (\Throwable $e) {
            Log::error('PesapalService::makePayment error: ' . $e->getMessage());
            $data['message'] = $e->getMessage();
        }

        return $data;
    }

    public function paymentConfirmation($paymentId, $payerId = null): array
    {
        $trackingId = $payerId ?: $paymentId;
        $data = [
            'success' => false,
            'data' => [
                'payment_status' => 'unpaid',
                'payment_method' => PESAPAL,
            ],
        ];

        try {
            if (!$trackingId) {
                throw new \Exception('Missing Pesapal order tracking ID.');
            }

            $token = $this->requestToken();
            $response = Http::withToken($token)
                ->acceptJson()
                ->withOptions(['verify' => !env('IS_LOCAL', false)])
                ->get($this->baseUrl . '/api/Transactions/GetTransactionStatus', [
                    'orderTrackingId' => $trackingId,
                ]);

            if (!$response->successful()) {
                throw new \Exception('Pesapal status request failed: ' . $response->body());
            }

            $body = $response->json();
            $status = strtoupper((string) ($body['payment_status_description'] ?? $body['status'] ?? 'PENDING'));

            if ($status === 'COMPLETED') {
                $data['success'] = true;
                $data['data']['payment_status'] = 'success';
            } else {
                $data['data']['payment_status'] = strtolower($status);
            }
        } catch (\Throwable $e) {
            Log::error('PesapalService::paymentConfirmation error: ' . $e->getMessage());
            $data['data']['message'] = $e->getMessage();
        }

        return $data;
    }

    public function saveProduct($data): array { return []; }
    public function subscribe($productId, $data = null): array { return []; }
    public function subscriptionCancel($subscriptionId, $data = null): array { return []; }
    public function subscriptionRemainingDays($subscriptionId, $data = null): array { return []; }
    public function subscriptionStatus($subscriptionId, $data = null): array { return []; }
    public function subscriptionRenewalDate($subscriptionId, $data = null): array { return []; }
    public function createWebhook(): array { return []; }
    public function handleWebhook($request): array { return []; }

    private function requestToken(): string
    {
        if ($this->consumerKey === '' || $this->consumerSecret === '') {
            throw new \Exception('Pesapal consumer key and secret are required. Please configure them in payment gateway settings.');
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withOptions(['verify' => !env('IS_LOCAL', false)])
            ->post($this->baseUrl . '/api/Auth/RequestToken', [
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret,
            ]);

        if (!$response->successful()) {
            throw new \Exception(
                'Pesapal token request failed (HTTP ' . $response->status() . '): ' . $response->body()
            );
        }

        $body = $response->json();

        // Pesapal returns an error object when credentials are wrong
        if (!empty($body['error'])) {
            $errMsg = $body['error']['message'] ?? json_encode($body['error']);
            throw new \Exception('Pesapal authentication error: ' . $errMsg);
        }

        $token = $body['token'] ?? null;
        if (!$token) {
            throw new \Exception(
                'Pesapal did not return an access token. API response: ' . json_encode($body)
            );
        }

        return $token;
    }

    private function resolveIpnId(string $token): string
    {
        if (
            !empty($this->gateway->url)
            && strlen((string) $this->gateway->url) > 20
            && !str_starts_with(strtolower((string) $this->gateway->url), 'http')
        ) {
            return (string) $this->gateway->url;
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->withOptions(['verify' => !env('IS_LOCAL', false)])
            ->post($this->baseUrl . '/api/URLSetup/RegisterIPN', [
                'url' => route('payment.pesapal.ipn'),
                'ipn_notification_type' => 'GET',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Pesapal IPN registration failed: ' . $response->body());
        }

        $body = $response->json();
        $ipnId = $body['ipn_id'] ?? $body['notification_id'] ?? null;

        if (!$ipnId) {
            throw new \Exception('Pesapal did not return an IPN ID.');
        }

        $this->gateway->url = $ipnId;
        $this->gateway->save();

        return $ipnId;
    }
}
