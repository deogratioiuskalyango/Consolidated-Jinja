<?php

namespace Botble\Hotel\Models;

use Botble\Base\Models\BaseModel;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends BaseModel
{
    protected $table = 'ht_bookings';

    protected $fillable = [
        'status',
        'amount',
        'sub_total',
        'coupon_amount',
        'coupon_code',
        'customer_id',
        'currency_id',
        'requests',
        'arrival_time',
        'number_of_guests',
        'payment_id',
        'transaction_id',
        'tax_amount',
    ];

    protected $casts = [
        'status' => BookingStatusEnum::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'ht_booking_services', 'booking_id', 'service_id');
    }

    public function address(): HasOne
    {
        return $this->hasOne(BookingAddress::class, 'booking_id')->withDefault();
    }

    public function room(): HasOne
    {
        return $this->hasOne(BookingRoom::class, 'booking_id')->withDefault();
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id')->withDefault();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'reference_id')->withDefault();
    }

    protected static function booted(): void
    {
        static::deleting(function (Booking $booking) {
            $booking->address()->delete();
            $booking->services()->detach();
            $booking->room()->delete();
        });
    }
}
