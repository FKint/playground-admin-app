<?php

namespace App\View\Components\FormHelpers;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BaseFormContent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct() {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Closure|string|View
    {
        return view('components.form-helpers.base-form-content');
    }
}
