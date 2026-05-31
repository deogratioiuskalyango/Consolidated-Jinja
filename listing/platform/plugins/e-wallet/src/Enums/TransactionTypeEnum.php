<?php

namespace Botble\EWallet\Enums;

use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

class TransactionTypeEnum extends Enum
{
    public const TOP_UP = 'top_up';

    public const PAYMENT = 'payment';

    public const REFUND = 'refund';

    public const ADMIN_ADJUSTMENT = 'admin_adjustment';

    public const VENDOR_PAYOUT = 'vendor_payout';

    public const WITHDRAWAL = 'withdrawal';

    public static $langPath = 'plugins/e-wallet::e-wallet.transaction.types';

    public function color(): string
    {
        return match ($this->value) {
            self::TOP_UP => 'success',
            self::PAYMENT => 'primary',
            self::REFUND => 'info',
            self::ADMIN_ADJUSTMENT => 'warning',
            self::VENDOR_PAYOUT => 'secondary',
            self::WITHDRAWAL => 'danger',
            default => 'secondary',
        };
    }

    public function badge(): string
    {
        return sprintf(
            '<span class="badge bg-%s text-white">%s</span>',
            $this->color(),
            $this->label()
        );
    }

    public function toHtml(): HtmlString
    {
        return new HtmlString($this->badge());
    }
}
