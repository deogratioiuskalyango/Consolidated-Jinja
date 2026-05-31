<?php

namespace Botble\EWallet\Helpers;

use Botble\Theme\Facades\Theme;

class WalletHelper
{
    public function isEnabled(): bool
    {
        return (bool) get_wallet_setting('enable_e_wallet', true);
    }

    public function allowNegativeBalance(): bool
    {
        return (bool) get_wallet_setting('allow_negative_balance', false);
    }

    public function isTopUpEnabled(): bool
    {
        return (bool) get_wallet_setting('enable_top_up', true);
    }

    public function getMinTopUp(): int
    {
        return (int) get_wallet_setting('min_top_up', 10);
    }

    public function getMaxTopUp(): int
    {
        return (int) get_wallet_setting('max_top_up', 100000000);
    }

    public function getDefaultCurrency(): string
    {
        return cms_currency()->getApplicationCurrency()->title ?? 'USD';
    }

    public function viewPath(string $view): string
    {
        $themeView = Theme::getThemeNamespace() . '::views.e-wallet.' . $view;

        if (view()->exists($themeView)) {
            return $themeView;
        }

        return 'plugins/e-wallet::themes.' . $view;
    }
}
