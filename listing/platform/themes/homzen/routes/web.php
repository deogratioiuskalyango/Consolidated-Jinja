<?php

use Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware;
use Botble\Base\Facades\BaseHelper;
use Botble\Theme\Facades\Theme;
use Botble\RealEstate\Http\Controllers\Fronts\RegisterController;
use Illuminate\Support\Facades\Route;
use Theme\Homzen\Http\Controllers\HomzenController;


Route::middleware(['web', 'core'])
    ->controller(HomzenController::class)
    ->group(function (): void {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function (): void {
            Route::get('login', 'getUnifiedLogin')->name('customer.login');
            Route::post('login', 'postUnifiedLogin')->name('customer.login.post');
            Route::get('wishlist', 'getWishlist')->name('public.wishlist');
            Route::get('hardware-quote', 'getHardwareQuote')->name('public.hardware-quote');
            Route::post('hardware-quote', 'postHardwareQuote')->name('public.hardware-quote.post');

            Route::middleware('account.guest')->name('public.account.')->group(function (): void {
                Route::get('account-login', 'redirectToUnifiedLogin')->name('login');
                Route::post('account-login', 'postUnifiedLogin')->name('login.post');
                Route::get('account-register', [RegisterController::class, 'showRegistrationForm'])->name('register');
                Route::post('account-register', [RegisterController::class, 'register'])->name('register.post');
            });

            Route::prefix('ajax')->name('public.ajax.')->middleware(RequiresJsonRequestMiddleware::class)->group(function (): void {
                Route::get('properties', 'ajaxGetProperties')->name('properties');
                Route::get('properties/map', 'ajaxGetPropertiesForMap')->name('properties.map');
                Route::get('projects', 'ajaxGetProjects')->name('projects');
                Route::get('projects/map', 'ajaxGetProjectsForMap')->name('projects.map');
                Route::get('projects/search', 'ajaxSearchProjects')->name('projects.search');
                Route::get('cities', 'ajaxGetCities')->name('cities');
            });
        });
    });

Route::middleware(['web', 'core', 'auth'])
    ->prefix(BaseHelper::getAdminPrefix())
    ->controller(HomzenController::class)
    ->group(function (): void {
        Route::get('hardware-quotations', 'getAdminHardwareQuotations')->name('admin.hardware-quotations.index');
    });

Theme::routes();
