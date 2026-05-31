<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Http\Requests\CouponRequest;
use Botble\Hotel\Models\Coupon;
use Botble\Hotel\Tables\CouponTable;
use Botble\JsValidation\Facades\JsValidator;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CouponController extends BaseController
{
    public function index(CouponTable $discountTable): View|JsonResponse
    {
        PageTitle::setTitle(trans('plugins/hotel::coupon.name'));

        return $discountTable->renderTable();
    }

    public function create(): View
    {
        Assets::usingVueJS()
            ->addStyles('timepicker')
            ->addScripts(['timepicker', 'form-validation'])
            ->addScriptsDirectly('vendor/core/plugins/hotel/js/coupon.js');

        $jsValidator = JsValidator::formRequest(CouponRequest::class);

        return view('plugins/hotel::coupons.create', compact('jsValidator'));
    }

    public function store(CouponRequest $request, BaseHttpResponse $response): BaseHttpResponse
    {
        $coupon = Coupon::query()->create(array_merge($request->validated(), $request->has('never_expired') ? [] : [
            'expires_date' => Carbon::parse(
                $request->input('expires_date') . ' ' . $request->input('expires_time')
            ),
        ]));

        event(new CreatedContentEvent(COUPON_MODULE_SCREEN_NAME, $request, $coupon));

        return $response
            ->setMessage(trans('plugins/hotel::coupon.created_message'))
            ->setNextUrl(route('coupons.edit', $coupon));
    }

    public function edit(Coupon $coupon): View
    {
        Assets::usingVueJS()
            ->addStyles('timepicker')
            ->addScripts(['timepicker', 'form-validation'])
            ->addScriptsDirectly('vendor/core/plugins/hotel/js/coupon.js');

        $jsValidator = JsValidator::formRequest(CouponRequest::class);

        return view('plugins/hotel::coupons.edit', compact('coupon', 'jsValidator'));
    }

    public function update(Coupon $coupon, CouponRequest $request, BaseHttpResponse $response): BaseHttpResponse
    {
        $coupon->update(array_merge($request->validated(), $request->has('never_expired') ? [] : [
            'expires_date' => Carbon::parse(
                $request->input('expires_date') . ' ' . $request->input('expires_time')
            ),
        ]));

        event(new UpdatedContentEvent(COUPON_MODULE_SCREEN_NAME, $request, $coupon));

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Coupon $coupon, BaseHttpResponse $response): BaseHttpResponse
    {
        $coupon->delete();

        return $response
            ->setMessage(trans('core/base::notices.delete_success_message'))
            ->setNextUrl(route('coupons.index'));
    }

    public function generateCouponCode(BaseHttpResponse $response): BaseHttpResponse
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (Coupon::query()->where('code', $code)->exists());

        return $response->setData($code);
    }
}
