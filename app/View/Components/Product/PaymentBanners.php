<?php

declare(strict_types=1);

namespace App\View\Components\Product;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PaymentBanners extends Component
{
    public function render(): View
    {
        return view('components.product.payment-banners');
    }
}
