<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as BaseMiddleware;

class PreventRequestForgery extends BaseMiddleware
{
    /**
     * The URIs that should be excluded from request forgery verification.
     *
     * @var array
     */
    protected $except = [
    ];
}
