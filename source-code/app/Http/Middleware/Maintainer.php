<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Maintainer
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->wantsJson()) {
                return $this->error([], __('Unauthorized'));
            }
            return redirect()->route('login');
        }

        // Primary maintainer role OR multi-role switch to 'maintainer'
        if ($user->role == USER_ROLE_MAINTAINER || session('active_role') === 'maintainer') {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return $this->error([], __('Unauthorized'));
        }

        abort(403);
    }
}
