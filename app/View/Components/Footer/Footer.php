<?php

declare(strict_types=1);

namespace App\View\Components\Footer;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    public function __construct()
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('components.footer.footer');
    }
}
