<?php

use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;
use Theme\FlexHome\Http\Controllers\FlexHomeController;

Route::group(['controller' => FlexHomeController::class, 'middleware' => ['web', 'core']], function (): void {
    Theme::registerRoutes(function (): void {
        Route::get('wishlist', 'getWishlist')->name('public.wishlist');

        Route::group(['prefix' => 'ajax', 'as' => 'public.ajax.'], function (): void {
            Route::get('cities', 'ajaxGetCities')->name('cities');
            Route::get('properties/map', 'ajaxGetPropertiesForMap')->name('properties.map');
            Route::get('projects-filter', 'ajaxGetProjectsFilter')->name('projects-filter');
        });
    });
});

Theme::routes();
