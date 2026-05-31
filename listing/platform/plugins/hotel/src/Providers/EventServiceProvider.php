<?php

namespace Botble\Hotel\Providers;

use Botble\Hotel\Events\BookingCreated;
use Botble\Hotel\Listeners\AddSitemapListener;
use Botble\Hotel\Listeners\GenerateInvoiceListener;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RenderingSiteMapEvent::class => [
            AddSitemapListener::class,
        ],
        BookingCreated::class => [
            GenerateInvoiceListener::class,
        ],
     ];
}
