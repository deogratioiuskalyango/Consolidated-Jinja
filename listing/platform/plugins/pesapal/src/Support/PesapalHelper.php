<?php

namespace Botble\Pesapal\Support;

use Botble\Pesapal\Libraries\OAuthConsumer;
use Botble\Pesapal\Libraries\OAuthRequest;
use Botble\Pesapal\Libraries\OAuthSignatureMethod_HMAC_SHA1;
use Exception;

class PesapalHelper
{
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $mode;
    protected string $baseUrl;

    public function __construct(string $consumerKey, string $consumerSecret, string $mode = 'sandbox')
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->mode = $mode;

        if ($mode === 'live') {
            $this->baseUrl = 'https://www.pesapal.com';
        } else {
            $this->baseUrl = 'https://cybqa.pesapal.com';
        }
    }

    public function generateIframeUrl(array $orderData, string $callbackUrl): ?string
    {
        try {
            // Build XML request data
            $xmlData = $this->buildXmlData($orderData);

            $token = null;
            $params = null;
            $signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
            $consumer = new OAuthConsumer($this->consumerKey, $this->consumerSecret);

            $apiUrl = $this->baseUrl . '/api/PostPesapalDirectOrderV4';

            // Create OAuth request
            $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $apiUrl, $params);
            $request->set_parameter('oauth_callback', $callbackUrl);
            $request->set_parameter('pesapal_request_data', $xmlData);
            $request->sign_request($signatureMethod, $consumer, $token);

            return $request->to_url();
        } catch (Exception $exception) {
            \Log::error('PesaPal Iframe URL Generation Error: ' . $exception->getMessage());

            return null;
        }
    }

    public function queryPaymentStatus(string $merchantReference, string $trackingId): ?string
    {
        try {
            $token = null;
            $params = null;
            $signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
            $consumer = new OAuthConsumer($this->consumerKey, $this->consumerSecret);

            $apiUrl = $this->baseUrl . '/api/QueryPaymentStatus';

            $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $apiUrl, $params);
            $request->set_parameter('pesapal_merchant_reference', $merchantReference);
            $request->set_parameter('pesapal_transaction_tracking_id', $trackingId);
            $request->sign_request($signatureMethod, $consumer, $token);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request->to_url());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($ch);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body = substr($response, $headerSize);

            curl_close($ch);

            // Parse response
            $elements = preg_split('/=/', $body);
            if (isset($elements[1])) {
                return trim($elements[1]);
            }

            return null;
        } catch (Exception $exception) {
            \Log::error('PesaPal Query Status Error: ' . $exception->getMessage());

            return null;
        }
    }

    protected function buildXmlData(array $orderData): string
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<PesapalDirectOrderInfo ';
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xml .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
        $xml .= 'Amount="' . htmlspecialchars($orderData['amount'], ENT_QUOTES, 'UTF-8') . '" ';
        $xml .= 'Description="' . htmlspecialchars($orderData['description'], ENT_QUOTES, 'UTF-8') . '" ';
        $xml .= 'Type="' . htmlspecialchars($orderData['type'], ENT_QUOTES, 'UTF-8') . '" ';
        $xml .= 'Reference="' . htmlspecialchars($orderData['reference'], ENT_QUOTES, 'UTF-8') . '" ';
        $xml .= 'FirstName="' . htmlspecialchars($orderData['first_name'], ENT_QUOTES, 'UTF-8') . '" ';
        $xml .= 'LastName="' . htmlspecialchars($orderData['last_name'], ENT_QUOTES, 'UTF-8') . '" ';
        $xml .= 'Email="' . htmlspecialchars($orderData['email'], ENT_QUOTES, 'UTF-8') . '" ';
        $xml .= 'PhoneNumber="' . htmlspecialchars($orderData['phone_number'] ?? '', ENT_QUOTES, 'UTF-8') . '" ';
        $xml .= 'xmlns="http://www.pesapal.com" />';

        return htmlentities($xml);
    }
}

