<?php

namespace Botble\EWallet\Http\Controllers;

use Botble\Base\Supports\Breadcrumb;
use Botble\EWallet\Models\Wallet;
use Botble\EWallet\Tables\WalletsTable;
use Botble\EWallet\Tables\WalletTransactionsTable;

class WalletController extends BaseWalletController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/e-wallet::e-wallet.wallet.list'), route('e-wallet.wallets.index'));
    }

    public function index(WalletsTable $table)
    {
        $this->pageTitle(trans('plugins/e-wallet::e-wallet.wallet.list'));

        return $table->renderTable();
    }

    public function show(int $id, WalletTransactionsTable $transactionsTable)
    {
        $wallet = Wallet::query()
            ->with('customer:id,name,email')
            ->findOrFail($id);

        $transactionsTable->forWallet($id);

        if (request()->has('draw')) {
            return $transactionsTable->render('core/table::base-table');
        }

        $this->pageTitle(trans('plugins/e-wallet::e-wallet.wallet.view_for', ['name' => $wallet->customer?->name ?? 'N/A']));

        return view('plugins/e-wallet::wallets.show', compact('wallet', 'transactionsTable'));
    }
}
