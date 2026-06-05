<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index()
    {
        return view('owner.domain.index', ['pageTitle' => __('Domain Config')]);
    }

    public function store(Request $request)
    {
        return back()->with('success', __('Domain settings saved.'));
    }

    public function info()
    {
        return response()->json(['status' => 200]);
    }
}
