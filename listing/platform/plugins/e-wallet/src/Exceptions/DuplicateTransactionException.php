<?php

namespace Botble\EWallet\Exceptions;

use Exception;

class DuplicateTransactionException extends Exception
{
    public function __construct(string $idempotencyKey)
    {
        parent::__construct(
            trans('plugins/e-wallet::e-wallet.errors.duplicate_transaction')
        );
    }
}
