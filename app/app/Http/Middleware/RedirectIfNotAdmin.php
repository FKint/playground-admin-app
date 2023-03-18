<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param null|string              $guard
     *
     * @return mixed
     */
    public function handle($request, \Closure $next, $guard = null)
    {
        $user = Auth::user();
        if (!$user->admin) {
            return redirect('/');
        }

        return $next($request);
    }
}
