<?php

declare(strict_types=1);

namespace App\Filament\User\Pages\Auth;

use App\DTO\SeoTags;
use App\Filament\User\Pages\Auth\Traits\HasSurname;
use Illuminate\View\View;

class Register extends \Filament\Auth\Pages\Register
{
    use HasSurname;

    public function render(): View
    {
        return view(
            'filament.user.pages.auth.register',
        )->layout(
            'layouts.app',
            [
                'seotags' => new SeoTags('noindex'),
            ]
        );
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->components([
                        $this->getNameFormComponent(),
                        $this->getSurNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}
