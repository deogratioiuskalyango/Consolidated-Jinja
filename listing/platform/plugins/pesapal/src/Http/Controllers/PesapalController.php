<?php

namespace Botble\Pesapal\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Pesapal\Support\PesapalHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PesapalController extends BaseController
{
    public function paymentCallback(Request $request, BaseHttpResponse $response)
    {
        $trackingId = $request->get('pesapal_transaction_tracking_id');
        $merchantReference = $request->get('pesapal_merchant_reference');

        if (! $trackingId || ! $merchantReference) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(trans('plugins/pesapal::pesapal.invalid_callback'));
        }

        $consumerKey = get_payment_setting('consumer_key', PESAPAL_PAYMENT_METHOD_NAME);
        $consumerSecret = get_payment_setting('consumer_secret', PESAPAL_PAYMENT_METHOD_NAME);
        $mode = get_payment_setting('mode', PESAPAL_PAYMENT_METHOD_NAME, 'sandbox');

        $helper = new PesapalHelper($consumerKey, $consumerSecret, $mode);
        $paymentStatus = $helper->queryPaymentStatus($merchantReference, $trackingId);

        if (! $paymentStatus) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(trans('plugins/pesapal::pesapal.failed_to_query_status'));
        }

        $status = PaymentStatusEnum::PENDING;

        if ($paymentStatus === 'COMPLETED') {
            $status = PaymentStatusEnum::COMPLETED;
        } elseif ($paymentStatus === 'FAILED') {
            $status = PaymentStatusEnum::FAILED;
        }

        // Get order to get actual amount
        $order = \Botble\Ecommerce\Models\Order::where('id', $merchantReference)->first();
        $amount = $order ? $order->amount : 0;
        $currency = $order ? cms_currency()->getDefaultCurrency()->title : 'KES';

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount' => $amount,
            'currency' => $currency,
            'charge_id' => $trackingId,
            'payment_channel' => PESAPAL_PAYMENT_METHOD_NAME,
            'status' => $status,
            'payment_type' => 'direct',
            'order_id' => [$merchantReference],
        ], $request);

        if ($status === PaymentStatusEnum::COMPLETED) {
            return $response
                ->setNextUrl(PaymentHelper::getRedirectURL())
                ->setMessage(trans('plugins/payment::payment.checkout_success'));
        }

        return $response
            ->setError()
            ->setNextUrl(PaymentHelper::getCancelURL())
            ->setMessage(trans('plugins/payment::payment.checkout_failed'));
    }

    public function ipnListener(Request $request, BaseHttpResponse $response)
    {
        $notificationType = $request->get('pesapal_notification_type');
        $trackingId = $request->get('pesapal_transaction_tracking_id');
        $merchantReference = $request->get('pesapal_merchant_reference');

        if ($notificationType !== 'CHANGE' || ! $trackingId || ! $merchantReference) {
            return response('Invalid request', 400);
        }

        $consumerKey = get_payment_setting('consumer_key', PESAPAL_PAYMENT_METHOD_NAME);
        $consumerSecret = get_payment_setting('consumer_secret', PESAPAL_PAYMENT_METHOD_NAME);
        $mode = get_payment_setting('mode', PESAPAL_PAYMENT_METHOD_NAME, 'sandbox');

        $helper = new PesapalHelper($consumerKey, $consumerSecret, $mode);
        $paymentStatus = $helper->queryPaymentStatus($merchantReference, $trackingId);

        if ($paymentStatus) {
            $status = PaymentStatusEnum::PENDING;

            if ($paymentStatus === 'COMPLETED') {
                $status = PaymentStatusEnum::COMPLETED;
            } elseif ($paymentStatus === 'FAILED') {
                $status = PaymentStatusEnum::FAILED;
            }

            // Get order to get actual amount
            $order = \Botble\Ecommerce\Models\Order::where('id', $merchantReference)->first();
            $amount = $order ? $order->amount : 0;
            $currency = $order ? cms_currency()->getDefaultCurrency()->title : 'KES';

            // Update payment status
            do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                'amount' => $amount,
                'currency' => $currency,
                'charge_id' => $trackingId,
                'payment_channel' => PESAPAL_PAYMENT_METHOD_NAME,
                'status' => $status,
                'payment_type' => 'direct',
                'order_id' => [$merchantReference],
            ], $request);

            // Return response as per PesaPal requirements
            $resp = "pesapal_notification_type=$notificationType&pesapal_transaction_tracking_id=$trackingId&pesapal_merchant_reference=$merchantReference";
            ob_start();
            echo $resp;
            ob_flush();
            exit;
        }

        return response('Error querying payment status', 500);
    }
}

