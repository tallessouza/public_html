<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RemainingCredit extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
		public string $style = 'default',
		public string $legendSize = 'md',
		public string $progressHeight = 'md',
		public string $labelWords = 'Words',
		public string $labelImages = 'Images'
	)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.remaining-credit');
    }
}
