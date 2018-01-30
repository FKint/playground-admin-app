<?php

namespace App\Providers;

use App\Child;
use App\ChildFamily;
use App\Policies\ChildFamilyPolicy;
use App\Policies\ChildPolicy;
use App\Policies\YearPolicy;
use App\Year;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Year::class => YearPolicy::class,
        Child::class => ChildPolicy::class,
        ChildFamily::class => ChildFamilyPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
