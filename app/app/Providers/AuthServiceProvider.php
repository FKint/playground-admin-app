<?php

namespace App\Providers;

use App\Models\Child;
use App\Models\ChildFamily;
use App\Models\Year;
use App\Policies\ChildFamilyPolicy;
use App\Policies\ChildPolicy;
use App\Policies\YearPolicy;
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
        ChildFamily::class => ChildFamilyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
