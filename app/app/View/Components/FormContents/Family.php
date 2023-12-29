<?php

namespace App\View\Components\FormContents;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Family extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $readonly = false,
        public bool $withId = false,
        public string $submitText = 'Opslaan'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Closure|string|View
    {
        return view('components.form-contents.family');
    }
}
