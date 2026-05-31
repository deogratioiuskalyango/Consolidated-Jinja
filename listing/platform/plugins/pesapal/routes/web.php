<?php

use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

// Register routes that need to be accessible from admin panel and external sources
Route::group(['namespace' => 'Botble\Pesapal\Http\Controllers'], function (): void {
    Route::post('pesapal/payment/ipn', [
        'as' => 'pesapal.payment.ipn',
        'uses' => 'PesapalController@ipnListener',
    ]);
});

// Register theme routes for callbacks
Theme::registerRoutes(function (): void {
    Route::group(['namespace' => 'Botble\Pesapal\Http\Controllers'], function (): void {
        Route::get('pesapal/payment/callback', [
            'as' => 'pesapal.payment.callback',
            'uses' => 'PesapalController@paymentCallback',
        ]);
    });
});

