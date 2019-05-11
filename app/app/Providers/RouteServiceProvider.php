<?php

namespace App\Providers;

use App;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        parent::boot();
        Route::model('year', App\Year::class);
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapInternalWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapInternalWebRoutes()
    {
        Route::prefix('internal')
            ->name('internal.')
            ->middleware(['auth', 'bindings'])
            ->namespace('\App\Http\Controllers\Internal')
            ->group(base_path('routes/internal.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api/{year}')
            ->name('api.')
            ->middleware(['api_auth', 'bindings', 'can:view,year'])
            ->namespace('\App\Http\Controllers\Internal')
            ->group(base_path('routes/api.php'));
    }
}
