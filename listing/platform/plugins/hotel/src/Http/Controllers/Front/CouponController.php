<?php

namespace Botble\Hotel\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Services\CouponService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CouponController extends BaseController
{
    public function __construct(protected BaseHttpResponse $response)
    {
        $this->middleware(function (Request $request, Closure $next) {
            if (! $request->ajax()) {
                abort(404);
            }

            return $next($request);
        });
    }

    public function apply(Request $request, CouponService $couponService): BaseHttpResponse
    {
        $request->validate([
            'coupon_code' => ['required', 'string'],
        ]);

        $coupon = $couponService->getCouponByCode($request->input('coupon_code'));

        if ($coupon === null) {
            return $this->response
                ->setError()
                ->setMessage(__('This coupon is invalid!'));
        } else {
            Session::put('applied_coupon_code', $request->input('coupon_code'));
        }

        Session::put('coupon_code', $request->input('coupon_code'));

        return $this->response
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $coupon->code]));
    }

    public function remove(): BaseHttpResponse
    {
        if (! Session::has('applied_coupon_code')) {
            return $this->response
                ->setError()
                ->setMessage(__('This coupon is not used yet!'));
        }

        Session::forget('applied_coupon_code');
        Session::forget('coupon_code');

        return $this->response
            ->setMessage(__('Removed coupon :code successfully!', ['code' => session('applied_coupon_code')]));
    }

    public function refresh(): BaseHttpResponse
    {
        return $this->response
            ->setData(view('plugins/hotel::coupons.partials.form')->render());
    }
}
