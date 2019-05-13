<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

class SetDefaultYearForUrls
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route()->hasParameter('year')) {
            URL::defaults(['year' => $request->route('year')->id]);
        } elseif ($request->user()) {
            $lastYear = $request->user()->organization->years()->orderBy('created_at', 'DESC')->first();
            if ($lastYear) {
                URL::defaults(['year' => $lastYear->id]);
            }
        }

        return $next($request);
    }
}
