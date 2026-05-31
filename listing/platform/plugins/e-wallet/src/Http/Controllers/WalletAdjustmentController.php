<?php

namespace Botble\EWallet\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Supports\Breadcrumb;
use Botble\EWallet\Forms\WalletAdjustmentForm;
use Botble\EWallet\Http\Requests\WalletAdjustmentRequest;
use Botble\EWallet\Models\Wallet;
use Botble\EWallet\Services\WalletService;

class WalletAdjustmentController extends BaseWalletController
{
    public function __construct(protected WalletService $walletService)
    {
        parent::__construct();
    }

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/e-wallet::e-wallet.wallet.list'), route('e-wallet.wallets.index'));
    }

    public function show(int $id)
    {
        $wallet = Wallet::query()
            ->with('customer:id,name,email')
            ->findOrFail($id);

        PageTitle::setTitle(trans('plugins/e-wallet::e-wallet.adjustment.adjust_for', ['name' => $wallet->customer?->name ?? 'N/A']));

        $form = WalletAdjustmentForm::createFromModel($wallet);

        return view('plugins/e-wallet::adjust-balance', compact('form', 'wallet'));
    }

    public function store(WalletAdjustmentRequest $request)
    {
        $walletId = $request->input('wallet_id');
        $wallet = Wallet::query()->findOrFail($walletId);

        $amountInDollars = (float) $request->input('amount');
        $amountCents = (int) round($amountInDollars * 100);
        $type = $request->input('adjustment_type');
        $reason = $request->input('reason');

        if ($type === 'debit') {
            $amountCents = -abs($amountCents);
        }

        $this->walletService->adjustBalance(
            customerId: $wallet->customer_id,
            amountCents: $amountCents,
            description: $reason,
            createdBy: auth()->id()
        );

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/e-wallet::e-wallet.adjustment.success'));
    }
}
