<?php

use App\Livewire\SearchBar;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(SearchBar::class)
        ->assertStatus(200);
});
