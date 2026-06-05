<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\DividendDeclaration;
use App\Models\DividendPayment;

class DividendController extends Controller
{
    public function index()
    {
        $shareholder = auth()->user()->shareholder;
        $data['pageTitle']    = __('Dividend History');
        $data['shareholder']  = $shareholder;
        $data['payments']     = DividendPayment::where('shareholder_id', $shareholder->id)
            ->with('declaration')
            ->orderByDesc('created_at')
            ->paginate(20);
        $data['totalReceived'] = DividendPayment::where('shareholder_id', $shareholder->id)
            ->where('status', DIVIDEND_STATUS_PAID)
            ->sum('amount');
        $data['pendingAmount'] = DividendPayment::where('shareholder_id', $shareholder->id)
            ->where('status', DIVIDEND_STATUS_DECLARED)
            ->sum('amount');
        return view('shareholder.dividends.index', $data);
    }

    public function show(DividendPayment $dividend)
    {
        $shareholder = auth()->user()->shareholder;
        abort_if($dividend->shareholder_id !== $shareholder->id, 403);
        $data['pageTitle']   = __('Dividend Details');
        $data['dividend']    = $dividend->load('declaration.declaredBy', 'declaration.resolution');
        $data['shareholder'] = $shareholder;
        return view('shareholder.dividends.show', $data);
    }
}
