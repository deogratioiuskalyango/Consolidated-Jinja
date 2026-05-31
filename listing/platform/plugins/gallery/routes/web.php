<?php

use Botble\Base\Facades\BaseHelper;
use Botble\Gallery\Models\Gallery;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Gallery\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'galleries', 'as' => 'galleries.'], function () {
            Route::resource('', 'GalleryController')->parameters(['' => 'gallery']);
        });
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        if (! theme_option('galleries_page_id')) {
            $prefix = SlugHelper::getPrefix(Gallery::class, 'galleries');

            Route::get($prefix ?: 'galleries', [
                'as' => 'public.galleries',
                'uses' => 'PublicController@getGalleries',
            ]);
        }
    });
});
