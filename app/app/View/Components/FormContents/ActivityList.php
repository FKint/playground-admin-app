<?php

namespace App\View\Components\FormContents;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ActivityList extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $readonly = false,
        public string $submitText = 'Opslaan',
        public bool $withId = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Closure|string|View
    {
        return view('components.form-contents.activity-list');
    }
}
