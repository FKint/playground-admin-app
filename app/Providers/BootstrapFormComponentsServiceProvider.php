<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Collective\Html\FormFacade as Form;

class BootstrapFormComponentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::component('bsText', 'components.form.text', ['name', 'attributes' => []]);
        Form::component('bsNumber', 'components.form.number', ['name', 'attributes' => []]);
        Form::component('bsDropdown', 'components.form.dropdown', ['name', 'choices' => [], 'attributes' => []]);
        Form::component('bsSubmit', 'components.form.submit', []);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
