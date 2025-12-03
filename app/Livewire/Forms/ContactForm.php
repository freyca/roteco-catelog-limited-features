<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Events\ContactFormSubmitted;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\View\View;
use Livewire\Component;

/**
 * @property \Filament\Schemas\Schema $form
 */
class ContactForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $contactFormData = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $schema = $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->placeholder(__('Name'))
                    ->hiddenLabel()
                    ->prefixIcon('heroicon-s-user')
                    ->maxLength(255),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->placeholder(__('Email'))
                    ->hiddenLabel()
                    ->prefixIcon('heroicon-s-envelope')
                    ->maxLength(255),
                Textarea::make('message')
                    ->required()
                    ->placeholder(__('Write your message here'))
                    ->hiddenLabel()
                    ->columnSpanFull(),
            ])->columns(['sm' => 1, 'lg' => 2]);

        return $schema->statePath('contactFormData');
    }

    public function submit(): void
    {
        ContactFormSubmitted::dispatch($this->form);

        session()->flash('contactFormSuccess');

        $this->redirect(route('contact'));
    }

    public function render(): View
    {
        return view('livewire.forms.contact-form');
    }
}
