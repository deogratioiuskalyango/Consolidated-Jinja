<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;

class BookingReportController extends BaseController
{
    public function index()
    {
        PageTitle::setTitle(trans('plugins/hotel::booking.reports'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/hotel/libraries/full-calendar-6.1.8/main.min.js',
            'vendor/core/plugins/hotel/js/booking-reports.js',
        ]);

        Assets::usingVueJS();

        return view('plugins/hotel::booking-reports');
    }
}
