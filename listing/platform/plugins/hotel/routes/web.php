<?php

use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Http\Controllers\CouponController;
use Botble\Hotel\Http\Controllers\Front\CouponController as CouponControllerFront;
use Botble\Hotel\Http\Controllers\InvoiceController;
use Botble\Hotel\Models\Place;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Models\Service;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Hotel\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix() . '/hotel', 'middleware' => 'auth'], function () {
        Route::get('settings', [
            'as' => 'hotel.settings',
            'uses' => 'HotelController@getSettings',
        ]);

        Route::post('settings', [
            'as' => 'hotel.settings.post',
            'uses' => 'HotelController@postSettings',
        ]);

        Route::get('invoice-settings', [
            'as' => 'hotel.invoice.settings',
            'uses' => 'InvoiceSettingController@getSettings',
        ]);

        Route::post('invoice-settings', [
            'as' => 'hotel.invoice.settings.post',
            'uses' => 'InvoiceSettingController@postSettings',
        ]);

        Route::group(['prefix' => 'rooms', 'as' => 'room.'], function () {
            Route::resource('', 'RoomController')->parameters(['' => 'room']);

            Route::get('room-availability/{id}', [
                'as' => 'availability',
                'uses' => 'RoomController@getRoomAvailability',
                'permission' => 'room.edit',
            ])->wherePrimaryKey();

            Route::post('room-availability/{id}', [
                'as' => 'availability.post',
                'uses' => 'RoomController@storeRoomAvailability',
                'permission' => 'room.edit',
            ])->wherePrimaryKey();

            Route::post('update-order-by', [
                'as' => 'update-order-by',
                'uses' => 'RoomController@postUpdateOrderby',
                'permission' => 'room.edit',
            ]);
        });

        Route::group(['prefix' => 'amenities', 'as' => 'amenity.'], function () {
            Route::resource('', 'AmenityController')->parameters(['' => 'amenity']);
        });

        Route::group(['prefix' => 'foods', 'as' => 'food.'], function () {
            Route::resource('', 'FoodController')->parameters(['' => 'food']);
        });

        Route::group(['prefix' => 'food-types', 'as' => 'food-type.'], function () {
            Route::resource('', 'FoodTypeController')->parameters(['' => 'food-type']);
        });

        Route::group(['prefix' => 'bookings', 'as' => 'booking.'], function () {
            Route::resource('', 'BookingController')->parameters(['' => 'booking'])->except(['create', 'store']);
        });

        Route::get('/booking-reports', [
            'uses' => 'BookingReportController@index',
            'as' => 'booking.reports.index',
        ]);

        Route::get('/booking-reports/bookings', [
            'uses' => 'BookingReportController@index',
            'as' => 'booking.reports.index',
            'permission' => 'booking.reports.index',
        ]);

        Route::get('/booking-reports/records', [
            'uses' => 'BookingReportRecordController@index',
            'as' => 'booking.reports.records.index',
            'permission' => 'booking.reports.index',
        ]);

        Route::group(['prefix' => 'customers', 'as' => 'customer.'], function () {
            Route::resource('', 'CustomerController')->parameters(['' => 'customer']);
        });

        Route::group(['prefix' => 'room-categories', 'as' => 'room-category.'], function () {
            Route::resource('', 'RoomCategoryController')->parameters(['' => 'room-category']);
        });

        Route::group(['prefix' => 'features', 'as' => 'feature.'], function () {
            Route::resource('', 'FeatureController')->parameters(['' => 'feature']);
        });

        Route::group(['prefix' => 'services', 'as' => 'service.'], function () {
            Route::resource('', 'ServiceController')->parameters(['' => 'service']);
        });

        Route::group(['prefix' => 'places', 'as' => 'place.'], function () {
            Route::resource('', 'PlaceController')->parameters(['' => 'place']);
        });

        Route::group(['prefix' => 'taxes', 'as' => 'tax.'], function () {
            Route::resource('', 'TaxController')->parameters(['' => 'tax']);
        });

        Route::prefix('invoice-template')->name('invoice-template.')->group(function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'InvoiceTemplateController@index',
                'permission' => 'hotel.invoice.index',
            ]);

            Route::put('/', [
                'as' => 'update',
                'uses' => 'InvoiceTemplateController@update',
                'permission' => 'hotel.invoice.index',
                'middleware' => 'preventDemo',
            ]);

            Route::post('reset', [
                'as' => 'reset',
                'uses' => 'InvoiceTemplateController@reset',
                'permission' => 'hotel.invoice.index',
                'middleware' => 'preventDemo',
            ]);

            Route::get('preview', [
                'as' => 'preview',
                'uses' => 'InvoiceTemplateController@preview',
                'permission' => 'hotel.invoice.index',
            ]);
        });

        Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function () {
            Route::resource('', 'InvoiceController')->parameters(['' => 'invoice']);
            Route::get('{id}', [InvoiceController::class, 'show'])
                ->name('show')
                ->wherePrimaryKey();
            Route::get('{id}/generate-invoice', 'InvoiceController@getGenerateInvoice')
                ->name('generate')
                ->wherePrimaryKey();
        });

        Route::group(['prefix' => 'reviews', 'as' => 'review.'], function () {
            Route::resource('', 'ReviewController')->parameters(['' => 'review'])->only(['index', 'destroy']);
        });

        Route::prefix('invoice-template')->name('invoice-template.')->group(function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'InvoiceTemplateController@index',
                'permission' => 'hotel.invoice.index',
            ]);

            Route::put('/', [
                'as' => 'update',
                'uses' => 'InvoiceTemplateController@update',
                'permission' => 'hotel.invoice.index',
                'middleware' => 'preventDemo',
            ]);

            Route::post('reset', [
                'as' => 'reset',
                'uses' => 'InvoiceTemplateController@reset',
                'permission' => 'hotel.invoice.index',
                'middleware' => 'preventDemo',
            ]);

            Route::get('preview', [
                'as' => 'preview',
                'uses' => 'InvoiceTemplateController@preview',
                'permission' => 'hotel.invoice.index',
            ]);
        });

        Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function () {
            Route::resource('', 'InvoiceController')->parameters(['' => 'invoice']);
            Route::get('{id}', [InvoiceController::class, 'show'])
                ->name('show')
                ->wherePrimaryKey();
            Route::get('{id}/generate-invoice', 'InvoiceController@getGenerateInvoice')
                ->name('generate')
                ->wherePrimaryKey();
        });

        Route::group(['prefix' => 'coupons', 'as' => 'coupons.'], function () {
            Route::resource('', CouponController::class)
                ->parameters(['' => 'coupon']);

            Route::post('generate-coupon', [
                'as' => 'generate-coupon',
                'uses' => 'CouponController@generateCouponCode',
                'permission' => 'coupons.index',
            ]);

            Route::delete('deletes', [
                'as' => 'deletes',
                'uses' => 'CouponController@deletes',
                'permission' => 'coupons.destroy',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
            Route::get(SlugHelper::getPrefix(Room::class, 'rooms'), 'PublicController@getRooms')->name('public.rooms');

            Route::get(SlugHelper::getPrefix(Room::class, 'rooms') . '/{slug}', 'PublicController@getRoom');

            Route::get(SlugHelper::getPrefix(Service::class, 'services') . '/{slug}', 'PublicController@getService');

            Route::get(SlugHelper::getPrefix(Place::class, 'places') . '/{slug}', 'PublicController@getPlace');

            Route::post('booking', 'PublicController@postBooking')->name('public.booking');
            Route::get('booking/{token}', 'PublicController@getBooking')->name('public.booking.form');

            Route::post('checkout', 'PublicController@postCheckout')->name('public.booking.checkout');

            Route::get('checkout/{transactionId}', 'PublicController@checkoutSuccess')
                ->name('public.booking.information');

            Route::prefix('coupon')->name('coupon.')->group(function () {
                Route::post('apply', [CouponControllerFront::class, 'apply'])->name('apply');
                Route::post('remove', [CouponControllerFront::class, 'remove'])->name('remove');
                Route::get('refresh', [CouponControllerFront::class, 'refresh'])->name('refresh');
            });

            Route::get('ajax/calculate-amount', 'PublicController@ajaxCalculateBookingAmount')
                ->name('public.booking.ajax.calculate-amount');

            Route::get('currency/switch/{code?}', [
                'as' => 'public.change-currency',
                'uses' => 'PublicController@changeCurrency',
            ]);
        });
    }
});

if (defined('THEME_MODULE_SCREEN_NAME')) {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::group([
            'namespace' => 'Botble\Hotel\Http\Controllers\Front\Customers',
            'middleware' => ['web', 'core', 'customer.guest'],
            'as' => 'customer.',
        ], function () {
            Route::get('login', 'LoginController@showLoginForm')->name('login');
            Route::post('login', 'LoginController@login')->name('login.post');

            Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
            Route::post('register', 'RegisterController@register')->name('register.post');

            Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.request');
            Route::post('password/reset', 'ResetPasswordController@reset')->name('password.reset.post');
            Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
            Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')
                ->name('password.reset.update');
        });

        Route::group([
            'namespace' => 'Botble\Hotel\Http\Controllers\Front\Customers',
            'middleware' => [
                'web',
                'core',
                HotelHelper::isEnableEmailVerification() ? 'customer' : 'customer.guest',
            ],
            'as' => 'customer.',
        ], function () {
            Route::get('register/confirm/resend', 'RegisterController@resendConfirmation')
                ->name('resend_confirmation');
            Route::get('register/confirm/{user}', 'RegisterController@confirm')
                ->name('confirm');
        });

        Route::group([
            'namespace' => 'Botble\Hotel\Http\Controllers\Front\Customers',
            'middleware' => ['web', 'core', 'customer'],
            'prefix' => 'customer',
            'as' => 'customer.',
        ], function () {
            Route::get('logout', 'LoginController@logout')->name('logout');

            Route::get('overview', [
                'as' => 'overview',
                'uses' => 'PublicController@getOverview',
            ]);

            Route::get('edit-account', [
                'as' => 'edit-account',
                'uses' => 'PublicController@getEditAccount',
            ]);

            Route::post('edit-account', [
                'as' => 'edit-account.post',
                'uses' => 'PublicController@postEditAccount',
            ]);

            Route::get('change-password', [
                'as' => 'change-password',
                'uses' => 'PublicController@getChangePassword',
            ]);

            Route::post('change-password', [
                'as' => 'post.change-password',
                'uses' => 'PublicController@postChangePassword',
            ]);

            Route::post('avatar', [
                'as' => 'avatar',
                'uses' => 'PublicController@postAvatar',
            ]);

            Route::get('bookings', [
                'as' => 'bookings',
                'uses' => 'BookingController@index',
            ]);

            Route::get('bookings/{id}', [
                'as' => 'bookings.show',
                'uses' => 'BookingController@show',
            ]);

            Route::get('generate-invoice/{id}', [
                'as' => 'generate-invoice',
                'uses' => 'BookingController@getGenerateInvoice',
            ])->wherePrimaryKey();

            if (HotelHelper::isReviewEnabled()) {
                Route::get('reviews', [
                    'as' => 'reviews',
                    'uses' => 'ReviewController@index',
                ]);
            }
        });
        if (HotelHelper::isReviewEnabled()) {
            Route::group([
                'namespace' => 'Botble\Hotel\Http\Controllers\Front',
                'middleware' => ['web', 'core'],
                'prefix' => 'customer',
                'as' => 'customer.',
            ], function () {
                Route::get('ajax/review/{key}', [
                    'as' => 'ajax.review.index',
                    'uses' => 'ReviewController@index',
                ]);
                Route::post('ajax/review/{key}', [
                    'as' => 'ajax.review.store',
                    'uses' => 'ReviewController@store',
                ]);
            });
        }
    });
}
