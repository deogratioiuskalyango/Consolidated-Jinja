<?php

namespace Botble\EWallet\Exceptions;

use Exception;

class WalletNotFoundException extends Exception
{
    public function __construct(int $customerId)
    {
        parent::__construct(
            trans('plugins/e-wallet::e-wallet.errors.wallet_not_found', ['id' => $customerId])
        );
    }
}
