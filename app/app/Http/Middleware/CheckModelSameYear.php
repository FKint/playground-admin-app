<?php

namespace App\Http\Middleware;

use Closure;

class CheckModelSameYear
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param array                    $models
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$models)
    {
        $year = $request->route('year');
        collect($models)->each(function ($model) use ($request, $year) {
            $object = $request->route($model);
            if ($object->year->id !== $year->id) {
                abort(404);
            }
        });

        return $next($request);
    }
}
