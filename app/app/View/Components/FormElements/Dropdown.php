<?php

namespace App\View\Components\FormElements;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public ?string $displayName = null,
        public array $choices,
        public ?string $value = null,
        public bool $readonly = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Closure|string|View
    {
        return view('components.form-elements.dropdown');
    }
}
