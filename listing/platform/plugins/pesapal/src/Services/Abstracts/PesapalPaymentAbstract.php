<?php

namespace Botble\Pesapal\Services\Abstracts;

use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Exception;
use Illuminate\Support\Arr;

abstract class PesapalPaymentAbstract
{
    use PaymentErrorTrait;

    protected ?string $paymentCurrency = null;

    protected bool $supportRefundOnline;

    protected float $totalAmount;

    public function __construct()
    {
        $this->paymentCurrency = config('plugins.payment.payment.currency');

        $this->totalAmount = 0;

        $this->supportRefundOnline = false;
    }

    public function getSupportRefundOnline(): bool
    {
        return $this->supportRefundOnline;
    }

    public function setCurrency($currency)
    {
        $this->paymentCurrency = $currency;

        return $this;
    }

    public function getCurrency()
    {
        return $this->paymentCurrency;
    }

    public function execute(array $data): ?string
    {
        try {
            return $this->makePayment($data);
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception, 1);

            return null;
        }
    }

    abstract public function makePayment(array $data): ?string;

    abstract public function afterMakePayment(string $chargeId, array $data): string;
}

