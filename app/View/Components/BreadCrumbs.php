<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Factories\BreadCrumbs\BreadCrumbsFactory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BreadCrumbs extends Component
{
    public function __construct(public BreadCrumbsFactory $breadcrumbs) {}

    public function render(): View
    {
        return view('components.bread-crumbs');
    }
}
