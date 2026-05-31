<?php

namespace Botble\EWallet\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(int $required, int $available)
    {
        parent::__construct(
            trans('plugins/e-wallet::e-wallet.errors.insufficient_balance', [
                'required' => format_price($required / 100),
                'available' => format_price($available / 100),
            ])
        );
    }
}
