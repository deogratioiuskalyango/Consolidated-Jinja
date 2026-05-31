<?php

use Botble\EWallet\Http\Controllers\Fronts\TopUpController;
use Botble\EWallet\Http\Controllers\Fronts\WalletController;
use Botble\EWallet\Http\Controllers\Fronts\WithdrawalController;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

Theme::registerRoutes(function (): void {
    Route::group([
        'middleware' => ['web', 'customer'],
        'prefix' => 'customer/e-wallet',
        'as' => 'customer.e-wallet.',
    ], function (): void {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('transactions', [WalletController::class, 'transactions'])->name('transactions');

        Route::group(['prefix' => 'topup', 'as' => 'topup.'], function (): void {
            Route::get('/', [TopUpController::class, 'create'])->name('create');
            Route::post('/', [TopUpController::class, 'store'])->name('store');
            Route::get('{code}/checkout', [TopUpController::class, 'checkout'])->name('checkout');
            Route::post('{code}/pay', [TopUpController::class, 'processPayment'])->name('pay');
            Route::get('{code}/callback', [TopUpController::class, 'callback'])->name('callback');
            Route::get('{code}/success', [TopUpController::class, 'success'])->name('success');
        });

        Route::group(['prefix' => 'withdrawals', 'as' => 'withdrawals.'], function (): void {
            Route::get('/', [WithdrawalController::class, 'index'])->name('index');
            Route::post('/', [WithdrawalController::class, 'store'])->name('store');
        });
    });
});
