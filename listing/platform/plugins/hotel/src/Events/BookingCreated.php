<?php

namespace Botble\Hotel\Events;

use Botble\Base\Events\Event;
use Botble\Hotel\Models\Booking;
use Illuminate\Queue\SerializesModels;

class BookingCreated extends Event
{
    use SerializesModels;

    public function __construct(public Booking $booking)
    {
    }
}
