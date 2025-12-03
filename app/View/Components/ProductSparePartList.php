<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class ProductSparePartList extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Collection $relatedSpareparts) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-spare-part-list');
    }
}
