<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

class FilamentCustomizationProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Filament colors
        FilamentColor::register([
            'primary' => Color::Zinc,
            'info' => Color::Blue,
            'warning' => Color::Yellow,
            'success' => Color::Green,
            'danger' => Color::Red,
        ]);

        // Notifications on left so does not overlap cart icon
        Notifications::alignment(Alignment::Start);
    }
}
