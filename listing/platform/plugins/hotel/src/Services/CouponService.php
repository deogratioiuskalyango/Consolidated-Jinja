<?php

namespace Botble\Hotel\Services;

use Botble\Base\Models\BaseModel;
use Botble\Hotel\Enums\CouponTypeEnum;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

class CouponService
{
    protected Booking|null $package = null;

    public function getCouponByCode(string $code): BaseModel|null
    {
        return Coupon::query()
            ->where('code', $code)
            ->where(function (Builder $query) {
                $query->whereNull('expires_date')
                    ->orWhere('expires_date', '>=', Carbon::now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('quantity')
                    ->orWhereColumn('quantity', '>', 'total_used');
            })
            ->first();
    }

    public function getDiscountAmount(string $type, float $value, float $amountTotal = 0): float
    {
        return match ($type) {
            CouponTypeEnum::PERCENTAGE => $value / 100 * $amountTotal,
            CouponTypeEnum::FIXED => $value,
            default => 0,
        };
    }

    public function getAmountAfterDiscount(float $discountAmount, float $amountTotal): float
    {
        return max($amountTotal - $discountAmount, 0);
    }

    public function forgotCouponSession(string|array $keys = []): void
    {
        Session::forget(array_merge((array) $keys, [
            'applied_coupon_code',
            'coupon_discount_amount',
            'cart_total',
        ]));
    }
}
