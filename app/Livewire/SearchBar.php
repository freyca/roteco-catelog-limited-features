<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Repositories\Database\SearchByName;
use Illuminate\View\View;
use Livewire\Component;

class SearchBar extends Component
{
    public string $searchTerm = '';

    public function render(): View
    {
        $results = [];

        // Do not search until 3 characters
        if (strlen($this->searchTerm) < 3) {
            return view('livewire.search-bar', [
                'results' => $results,
            ]);
        }

        $results = SearchByName::search($this->searchTerm);

        return view('livewire.search-bar', [
            'results' => $results,
        ]);
    }
}
