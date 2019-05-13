<?php

namespace App\Providers;

use Collective\Html\FormFacade as Form;
use Illuminate\Support\ServiceProvider;

class BootstrapFormComponentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Form::component('bsText', 'components.form.text', ['name', 'display_name' => null, 'attributes' => []]);
        Form::component('bsTextarea', 'components.form.textarea', ['name', 'display_name' => null, 'attributes' => []]);
        Form::component('bsNumber', 'components.form.number', ['name', 'display_name' => null, 'attributes' => []]);
        Form::component('bsDropdown', 'components.form.dropdown', ['name', 'display_name' => null, 'choices' => [], 'attributes' => []]);
        Form::component('bsCheckbox', 'components.form.checkbox', ['name', 'display_name' => null, 'attributes' => []]);
        Form::component('bsSubmit', 'components.form.submit', ['text' => 'Opslaan']);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
