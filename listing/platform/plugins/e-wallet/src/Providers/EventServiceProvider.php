<?php

namespace Botble\EWallet\Providers;

use Botble\Ecommerce\Events\OrderReturnedEvent;
use Botble\EWallet\Listeners\ProcessOrderRefund;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderReturnedEvent::class => [
            ProcessOrderRefund::class,
        ],
    ];
}
