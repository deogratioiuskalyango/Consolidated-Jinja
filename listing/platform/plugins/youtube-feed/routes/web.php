<?php

use Botble\Base\Facades\AdminHelper;
use Botble\YoutubeFeed\Http\Controllers\PublicYoutubeVideosController;
use Botble\YoutubeFeed\Http\Controllers\YoutubeFeedSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('api/videos', PublicYoutubeVideosController::class)->name('public.youtube.videos');

Route::group(['namespace' => 'Botble\YoutubeFeed\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'youtube-feed', 'as' => 'youtube-feed.'], function (): void {
            Route::get('settings', [
                'as' => 'settings',
                'uses' => 'YoutubeFeedSettingsController@edit',
                'permission' => 'youtube-feed.settings',
            ]);
            Route::put('settings', [
                'as' => 'settings.update',
                'uses' => 'YoutubeFeedSettingsController@update',
                'permission' => 'youtube-feed.settings',
            ]);
        });
    });
});
