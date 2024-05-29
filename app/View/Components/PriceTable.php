<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PriceTable extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
		public string $title = 'Free Trial',
		public string $currency = '$',
		public string $price = '0',
		public string $period = 'month',
		public string $buttonLabel = 'Select Basic',
		public string $buttonLink = '#',
		public string $activeFeatures = 'Unlimited; 30+ Languages; Custom Templates',
		public string $inactiveFeatures = 'Full API Integration; VIP Support',
		public string $totalWords = '0',
		public string $totalImages = '0',
		public int $trialDays = 0,
		public bool $featured = false,
	)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.price-table');
    }
}
