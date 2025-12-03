<?php

declare(strict_types=1);

namespace App\Livewire\Buttons;

use Illuminate\View\View;
use Livewire\Component;

class FilterButton extends Component
{
    public function toggleFilterBar(): void
    {
        $this->dispatch('toggleFilterBar');
    }

    public function render(): View
    {
        return view('livewire.buttons.filter-button');
    }
}
