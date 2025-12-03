<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\SeoTags;
use App\Factories\BreadCrumbs\StandardPageBreadCrumbs;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('pages.cart', [
            'seotags' => new SeoTags('noindex'),
            'breadcrumbs' => new StandardPageBreadCrumbs([
                __('Cart') => route('checkout.cart'), // @phpstan-ignore-line
            ]),
        ]);
    }
}
