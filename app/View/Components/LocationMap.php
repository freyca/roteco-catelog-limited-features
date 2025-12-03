<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LocationMap extends Component
{
    public function render(): View
    {
        return view('components.location-map');
    }
}
