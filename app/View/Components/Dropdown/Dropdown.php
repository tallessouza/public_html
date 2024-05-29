<?php

namespace App\View\Components\Dropdown;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
		public string $anchor = 'start',
		public string $offsetY = '',
		public string $triggerType = 'hover',
	){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown.dropdown');
    }
}
